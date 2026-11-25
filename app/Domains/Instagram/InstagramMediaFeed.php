<?php

namespace App\Domains\Instagram;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use UnexpectedValueException;

final class InstagramMediaFeed
{
    private const POST_LIMIT = 16;
    private const MAX_CAROUSEL_IMAGES = 10;

    public function __construct(
        private InstagramAuth $instagramAuth,
        private InstagramApiClient $instagramApiClient,
        private InstagramImageWatermarker $instagramImageWatermarker,
    ) {}

    /**
     * @return array<int, array{permalink: string, images: array<int, array{imageKey: string, altText: string}>}>|null
     */
    public function latestImages(): ?array
    {
        $configuration = $this->instagramAuth->credentials();

        if ($configuration === null) {
            return null;
        }

        return array_map(
            static fn (array $post): array => [
                'permalink' => $post['permalink'],
                'images' => array_map(
                    static fn (array $image): array => [
                        'imageKey' => $image['imageKey'],
                        'altText' => $image['altText'],
                    ],
                    $post['images'],
                ),
            ],
            $this->cachedImages($configuration),
        );
    }

    public function refreshImages(): void
    {
        $configuration = $this->instagramAuth->credentials();

        if ($configuration === null) {
            throw new UnexpectedValueException('Instagram media feed is not configured.');
        }

        $posts = $this->fetchImages($configuration);

        $this->cacheImages(array_map(
            static fn (array $post): array => $post['images'][0],
            $posts,
        ));

        Cache::forever(
            $this->cachedImagesKey($configuration['apiVersion'], $configuration['userId']),
            $posts,
        );

        Log::info('Instagram media feed cache refreshed.', [
            'post_count' => count($posts),
        ]);
    }

    public function clearCache(): void
    {
        $identifiers = $this->instagramAuth->configurationIdentifiers();

        if ($identifiers === null) {
            throw new UnexpectedValueException('Instagram media feed cache cannot be cleared because the feed is not configured.');
        }

        $disk = config('filesystems.default');

        if (! is_string($disk) || $disk === '') {
            throw new UnexpectedValueException('The default filesystem disk is not configured.');
        }

        if (! Storage::disk($disk)->deleteDirectory('instagram-images')) {
            throw new UnexpectedValueException('Cached Instagram images could not be deleted.');
        }

        Cache::forget($this->cachedImagesKey($identifiers['apiVersion'], $identifiers['userId']));
    }

    public function cacheImage(string $imageKey): bool
    {
        $configuration = $this->instagramAuth->credentials();

        if ($configuration === null) {
            return false;
        }

        foreach ($this->cachedImages($configuration) as $post) {
            foreach ($post['images'] as $image) {
                if ($image['imageKey'] === $imageKey) {
                    $this->cacheImages([$image]);

                    return true;
                }
            }
        }

        return false;
    }

    private function cachedImagesKey(string $apiVersion, string $userId): string
    {
        return 'instagram.cached-images.v3.'.$apiVersion.'.'.$userId;
    }

    /**
     * @param  array{userId: string, accessToken: string, apiVersion: string}  $configuration
     * @return array<int, array{permalink: string, images: array<int, array{imageUrl: string, imageKey: string, altText: string}>}>
     */
    private function cachedImages(array $configuration): array
    {
        $posts = Cache::get($this->cachedImagesKey($configuration['apiVersion'], $configuration['userId']), []);

        if (! is_array($posts)) {
            throw new UnexpectedValueException('Cached Instagram media has an invalid format.');
        }

        return $posts;
    }

    /**
     * @param  array{userId: string, accessToken: string, apiVersion: string}  $configuration
     * @return array<int, array{permalink: string, images: array<int, array{imageUrl: string, imageKey: string, altText: string}>}>
     */
    private function fetchImages(array $configuration): array
    {
        $mediaItems = $this->instagramApiClient->fetchMedia(
            $configuration['apiVersion'],
            $configuration['userId'],
            $configuration['accessToken'],
        );

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
            $permalink = $mediaItem['permalink'] ?? null;

            if (! is_string($permalink) || ! $this->isInstagramPermalink($permalink)) {
                continue;
            }

            $imageUrls = match ($mediaItem['media_type'] ?? null) {
                'IMAGE' => is_string($mediaItem['media_url'] ?? null)
                    ? [$mediaItem['media_url']]
                    : [],
                'CAROUSEL_ALBUM' => $this->carouselImageUrls($mediaItem),
                default => null,
            };

            $validImageUrls = [];

            foreach (is_array($imageUrls) ? $imageUrls : [] as $imageUrl) {
                if (is_string($imageUrl) && $this->isInstagramMediaUrl($imageUrl)) {
                    $validImageUrls[] = $imageUrl;
                }
            }

            $altText = $mediaItem['alt_text'] ?? null;
            $hasAltText = is_string($altText) && trim($altText) !== '';
            $slideCount = count($validImageUrls);
            $slides = [];

            foreach ($validImageUrls as $slideIndex => $imageUrl) {
                $imageKey = $slideIndex === 0
                    ? hash('sha256', $permalink)
                    : hash('sha256', $permalink.'#'.$slideIndex);

                $slides[] = [
                    'imageUrl' => $imageUrl,
                    'imageKey' => $imageKey,
                    'altText' => $hasAltText
                    ? $altText
                        : ($slideCount > 1
                            ? 'Photo '.($slideIndex + 1).' of '.$slideCount.' from a Project City Build Instagram post'
                            : 'Project City Build Instagram post'),
                ];
            }

            if ($slides === []) {
                continue;
            }

            $images[] = [
                'permalink' => $permalink,
                'images' => $slides,
            ];

            if (count($images) === self::POST_LIMIT) {
                break;
            }
        }

        return $images;
    }

    /**
     * @param  array<int, array{imageUrl: string, imageKey: string, altText: string}>  $images
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

        foreach ($this->instagramApiClient->downloadImages($imagesToDownload) as $imageKey => $contents) {
            $contents = $this->instagramImageWatermarker->watermark($contents);

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
     * @return array<int, string>
     */
    private function carouselImageUrls(array $mediaItem): array
    {
        $children = $mediaItem['children']['data'] ?? null;
        $imageUrls = [];

        if (is_array($children)) {
            foreach ($children as $child) {
                if (
                    is_array($child)
                    && ($child['media_type'] ?? null) === 'IMAGE'
                    && is_string($child['media_url'] ?? null)
                ) {
                    $imageUrls[] = $child['media_url'];
                }
            }
        }

        return array_slice($imageUrls, 0, self::MAX_CAROUSEL_IMAGES);
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
