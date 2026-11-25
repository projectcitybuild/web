<?php

namespace App\Core\Domains\Instagram;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use UnexpectedValueException;

final class InstagramMediaFeed
{
    private const CACHE_TTL_SECONDS = 3600;
    private const IMAGE_LIMIT = 8;

    public function isConfigured(): bool
    {
        return $this->configuration() !== null;
    }

    /**
     * @return array<int, array{imageUrl: string, permalink: string, altText: string}>|null
     */
    public function latestImages(): ?array
    {
        $configuration = $this->configuration();

        if ($configuration === null) {
            return null;
        }

        try {
            return Cache::remember(
                'instagram.latest-images.'.$configuration['apiVersion'].'.'.$configuration['userId'],
                now()->addSeconds(self::CACHE_TTL_SECONDS),
                fn (): array => $this->fetchImages($configuration),
            );
        } catch (ConnectionException|RequestException|UnexpectedValueException $exception) {
            Log::warning('Instagram media could not be loaded.', [
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
     * @return array<int, array{imageUrl: string, permalink: string, altText: string}>
     */
    private function fetchImages(array $configuration): array
    {
        $response = Http::connectTimeout(3)
            ->timeout(5)
            ->get(
                'https://graph.facebook.com/'.$configuration['apiVersion'].'/'.$configuration['userId'].'/media',
                [
                    'fields' => 'alt_text,caption,media_type,media_url,permalink,timestamp,children{media_type,media_url}',
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

            if (! $this->isSecureUrl($imageUrl) || ! $this->isInstagramPermalink($permalink)) {
                continue;
            }

            $altText = $mediaItem['alt_text'] ?? null;
            $caption = $mediaItem['caption'] ?? null;

            if (! is_string($altText) || trim($altText) === '') {
                $altText = is_string($caption) && trim($caption) !== ''
                    ? Str::limit(trim($caption), 160)
                    : 'Project City Build Instagram post';
            }

            $images[] = [
                'imageUrl' => $imageUrl,
                'permalink' => $permalink,
                'altText' => $altText,
            ];

            if (count($images) === self::IMAGE_LIMIT) {
                break;
            }
        }

        return $images;
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

    private function isSecureUrl(mixed $url): bool
    {
        return is_string($url)
            && filter_var($url, FILTER_VALIDATE_URL) !== false
            && parse_url($url, PHP_URL_SCHEME) === 'https';
    }

    private function isInstagramPermalink(mixed $url): bool
    {
        return $this->isSecureUrl($url)
            && parse_url($url, PHP_URL_HOST) === 'www.instagram.com';
    }
}
