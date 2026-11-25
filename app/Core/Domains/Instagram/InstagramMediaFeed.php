<?php

namespace App\Core\Domains\Instagram;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToWriteFile;
use UnexpectedValueException;

final class InstagramMediaFeed
{
    private const CACHE_TTL_SECONDS = 3600;
    private const IMAGE_LIMIT = 8;
    private const MAX_IMAGE_BYTES = 10485760;

    public function isConfigured(): bool
    {
        return $this->configuration() !== null;
    }

    /**
     * @return array<int, array{imageKey: string, permalink: string, altText: string}>|null
     */
    public function latestImages(): ?array
    {
        $configuration = $this->configuration();

        if ($configuration === null) {
            return null;
        }

        try {
            return Cache::remember(
                'instagram.cached-images.v2.'.$configuration['apiVersion'].'.'.$configuration['userId'],
                now()->addSeconds(self::CACHE_TTL_SECONDS),
                fn (): array => $this->fetchImages($configuration),
            );
        } catch (ConnectionException|RequestException|UnableToWriteFile|UnexpectedValueException $exception) {
            Log::warning('Instagram media could not be loaded or cached.', [
                'exception_class' => $exception::class,
                'status' => $exception instanceof RequestException
                    ? $exception->response->status()
                    : null,
            ]);

            return null;
        }
    }

    /**
     * @return array{userId: string, accessToken: string, apiVersion: string}|null
     */
    private function configuration(): ?array
    {
        $userId = config('services.instagram.user_id');
        $accessToken = config('services.instagram.access_token');
        $apiVersion = config('services.instagram.api_version');

        if (
            ! is_string($userId)
            || ! ctype_digit($userId)
            || ! is_string($accessToken)
            || $accessToken === ''
            || ! is_string($apiVersion)
            || preg_match('/^v\d+\.\d+$/D', $apiVersion) !== 1
        ) {
            return null;
        }

        return [
            'userId' => $userId,
            'accessToken' => $accessToken,
            'apiVersion' => $apiVersion,
        ];
    }

    /**
     * @param  array{userId: string, accessToken: string, apiVersion: string}  $configuration
     * @return array<int, array{imageKey: string, permalink: string, altText: string}>
     */
    private function fetchImages(array $configuration): array
    {
        $response = Http::connectTimeout(3)
            ->timeout(5)
            ->get(
                'https://graph.instagram.com/'.$configuration['apiVersion'].'/'.$configuration['userId'].'/media',
                [
                    'fields' => 'alt_text,media_type,media_url,permalink,timestamp,children{media_type,media_url}',
                    'limit' => 50,
                    'access_token' => $configuration['accessToken'],
                ],
            )
            ->throw();

        $mediaItems = $response->json('data');

        if (! is_array($mediaItems)) {
            throw new UnexpectedValueException('Instagram Graph API returned an invalid media list.');
        }

        $mediaItems = array_values(array_filter($mediaItems, is_array(...)));

        usort(
            $mediaItems,
            static fn (array $left, array $right): int => strcmp(
                is_string($right['timestamp'] ?? null) ? $right['timestamp'] : '',
                is_string($left['timestamp'] ?? null) ? $left['timestamp'] : '',
            ),
        );

        $images = [];

        foreach ($mediaItems as $mediaItem) {
            $imageUrl = match ($mediaItem['media_type'] ?? null) {
                'IMAGE' => $mediaItem['media_url'] ?? null,
                'CAROUSEL_ALBUM' => $this->firstCarouselImageUrl($mediaItem),
                default => null,
            };
            $permalink = $mediaItem['permalink'] ?? null;

            if (! $this->isInstagramMediaUrl($imageUrl) || ! $this->isInstagramPermalink($permalink)) {
                continue;
            }

            $altText = $mediaItem['alt_text'] ?? null;

            $images[] = [
                'imageUrl' => $imageUrl,
                'imageKey' => hash('sha256', $permalink),
                'permalink' => $permalink,
                'altText' => is_string($altText) && trim($altText) !== ''
                    ? $altText
                    : 'Project City Build Instagram post',
            ];

            if (count($images) === self::IMAGE_LIMIT) {
                break;
            }
        }

        $this->cacheImages($images);

        return array_map(
            static fn (array $image): array => [
                'imageKey' => $image['imageKey'],
                'permalink' => $image['permalink'],
                'altText' => $image['altText'],
            ],
            $images,
        );
    }

    /**
     * @param  array<int, array{imageUrl: string, imageKey: string, permalink: string, altText: string}>  $images
     */
    private function cacheImages(array $images): void
    {
        $disk = config('filesystems.default');

        if (! is_string($disk) || $disk === '') {
            throw new UnexpectedValueException('The default filesystem disk is not configured.');
        }

        $storage = Storage::disk($disk);
        $imagesToDownload = [];

        foreach ($images as $image) {
            if (! $storage->exists($this->imagePath($image['imageKey']))) {
                $imagesToDownload[$image['imageKey']] = $image;
            }
        }

        if ($imagesToDownload === []) {
            return;
        }

        $responses = Http::pool(static function (Pool $pool) use ($imagesToDownload): array {
            $requests = [];

            foreach ($imagesToDownload as $imageKey => $image) {
                $requests[$imageKey] = $pool->as($imageKey)
                    ->connectTimeout(3)
                    ->timeout(5)
                    ->withOptions(['allow_redirects' => false])
                    ->get($image['imageUrl']);
            }

            return $requests;
        });

        foreach ($responses as $imageKey => $response) {
            if ($response instanceof ConnectionException) {
                throw $response;
            }

            $response->throw();

            $contentType = $response->header('Content-Type');
            $mimeType = is_string($contentType)
                ? strtolower(trim(explode(';', $contentType, 2)[0]))
                : '';
            $contents = $response->body();

            if (
                $mimeType !== 'image/jpeg'
                || ! str_starts_with($contents, "\xFF\xD8\xFF")
                || strlen($contents) > self::MAX_IMAGE_BYTES
            ) {
                throw new UnexpectedValueException('Instagram returned an invalid image.');
            }

            if (! $storage->put($this->imagePath($imageKey), $contents)) {
                throw new UnexpectedValueException('Instagram image could not be written to storage.');
            }
        }
    }

    private function imagePath(string $imageKey): string
    {
        return 'instagram-images/'.$imageKey.'.jpg';
    }

    /**
     * @param  array<string, mixed>  $mediaItem
     */
    private function firstCarouselImageUrl(array $mediaItem): mixed
    {
        $children = $mediaItem['children']['data'] ?? null;

        if (is_array($children)) {
            foreach ($children as $child) {
                if (
                    is_array($child)
                    && ($child['media_type'] ?? null) === 'IMAGE'
                    && is_string($child['media_url'] ?? null)
                ) {
                    return $child['media_url'];
                }
            }
        }

        return $mediaItem['media_url'] ?? null;
    }

    private function isInstagramMediaUrl(mixed $url): bool
    {
        if (! is_string($url) || filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        $parts = parse_url($url);

        if (! is_array($parts)) {
            return false;
        }

        $host = is_string($parts['host'] ?? null) ? strtolower($parts['host']) : '';
        $isInstagramCdn = $host === 'cdninstagram.com'
            || str_ends_with($host, '.cdninstagram.com');
        $isFacebookCdn = $host === 'fbcdn.net'
            || str_ends_with($host, '.fbcdn.net');

        return ($parts['scheme'] ?? null) === 'https'
            && ! isset($parts['user'])
            && ! isset($parts['pass'])
            && ! isset($parts['port'])
            && ($isInstagramCdn || $isFacebookCdn);
    }

    private function isInstagramPermalink(mixed $url): bool
    {
        return is_string($url)
            && filter_var($url, FILTER_VALIDATE_URL) !== false
            && parse_url($url, PHP_URL_SCHEME) === 'https'
            && parse_url($url, PHP_URL_HOST) === 'www.instagram.com';
    }
}
