<?php

namespace App\Domains\Instagram;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;
use UnexpectedValueException;

final class InstagramMediaFeed
{
    private const POST_LIMIT = 16;

    public function __construct(
        private InstagramAuth $instagramAuth,
        private InstagramApiClient $instagramApiClient,
        private InstagramMediaNormalizer $instagramMediaNormalizer,
        private InstagramImageDownloader $instagramImageDownloader,
        private InstagramImageWatermarker $instagramImageWatermarker,
    ) {}

    /**
     * @return array<int, array{permalink: string, images: array<int, array{imageKey: string, altText: string}>}>|null
     */
    public function latestImages(): ?array
    {
        $identifiers = $this->instagramAuth->configurationIdentifiers();

        if ($identifiers === null) {
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
            $this->cachedSnapshot($identifiers['apiVersion'], $identifiers['userId'])['posts'],
        );
    }

    public function lastSuccessfulRefreshAt(): ?string
    {
        $identifiers = $this->instagramAuth->configurationIdentifiers();

        if ($identifiers === null) {
            return null;
        }

        return $this->cachedSnapshot($identifiers['apiVersion'], $identifiers['userId'])['refreshedAt'];
    }

    public function refreshImages(): void
    {
        $configuration = $this->instagramAuth->credentials();

        if ($configuration === null) {
            throw new UnexpectedValueException('Instagram media feed is not configured.');
        }

        try {
            $posts = $this->fetchImages($configuration);

            $this->cacheImages(array_map(
                static fn (array $post): array => $post['images'][0],
                $posts,
            ));

            Cache::forever(
                $this->cachedImagesKey($configuration['apiVersion'], $configuration['userId']),
                [
                    'refreshedAt' => now()->toIso8601String(),
                    'posts' => $posts,
                ],
            );

            Log::info('Instagram media feed cache refreshed.', [
                'post_count' => count($posts),
            ]);
        } catch (Throwable $exception) {
            Log::error('Instagram media feed refresh failed.', [
                'exception' => $exception::class,
            ]);

            throw $exception;
        }
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
        $identifiers = $this->instagramAuth->configurationIdentifiers();

        if ($identifiers === null) {
            return false;
        }

        foreach ($this->cachedSnapshot($identifiers['apiVersion'], $identifiers['userId'])['posts'] as $post) {
            foreach ($post['images'] as $image) {
                if ($image['imageKey'] === $imageKey) {
                    try {
                        $this->cacheImages([$image]);
                    } catch (Throwable $exception) {
                        Log::warning('Instagram carousel image cache fill failed.', [
                            'image_key' => $imageKey,
                            'exception' => $exception::class,
                        ]);

                        return false;
                    }

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
     * @return array{
     *     refreshedAt: string|null,
     *     posts: array<int, array{permalink: string, images: array<int, array{imageUrl: string, imageKey: string, altText: string}>}>
     * }
     */
    private function cachedSnapshot(string $apiVersion, string $userId): array
    {
        $snapshot = Cache::get($this->cachedImagesKey($apiVersion, $userId), []);

        if (! is_array($snapshot)) {
            throw new UnexpectedValueException('Cached Instagram media has an invalid format.');
        }

        if (array_is_list($snapshot)) {
            return [
                'refreshedAt' => null,
                'posts' => $snapshot,
            ];
        }

        if (
            ! is_array($snapshot['posts'] ?? null)
            || ! array_is_list($snapshot['posts'])
            || (! is_string($snapshot['refreshedAt'] ?? null) && ($snapshot['refreshedAt'] ?? null) !== null)
        ) {
            throw new UnexpectedValueException('Cached Instagram media has an invalid format.');
        }

        return [
            'refreshedAt' => $snapshot['refreshedAt'] ?? null,
            'posts' => $snapshot['posts'],
        ];
    }

    /**
     * @param  array{userId: string, accessToken: string, apiVersion: string}  $configuration
     * @return array<int, array{permalink: string, images: array<int, array{imageUrl: string, imageKey: string, altText: string}>}>
     */
    private function fetchImages(array $configuration): array
    {
        $posts = [];
        $seenPermalinks = [];
        $seenCursors = [];
        $cursor = null;

        while (count($posts) < self::POST_LIMIT) {
            $response = $this->instagramApiClient->fetchMedia(
                $configuration['apiVersion'],
                $configuration['userId'],
                $configuration['accessToken'],
                $cursor,
            );
            $page = $this->instagramMediaNormalizer->normalizePage($response);

            foreach ($page['posts'] as $post) {
                if (isset($seenPermalinks[$post['permalink']])) {
                    continue;
                }

                $seenPermalinks[$post['permalink']] = true;
                $posts[] = $post;
            }

            if (count($posts) >= self::POST_LIMIT || $page['nextCursor'] === null) {
                break;
            }

            if (isset($seenCursors[$page['nextCursor']])) {
                throw new UnexpectedValueException('Instagram Graph API returned a repeated pagination cursor.');
            }

            $seenCursors[$page['nextCursor']] = true;
            $cursor = $page['nextCursor'];
        }

        usort($posts, static function (array $left, array $right): int {
            $leftTimestamp = is_string($left['timestamp']) ? strtotime($left['timestamp']) : false;
            $rightTimestamp = is_string($right['timestamp']) ? strtotime($right['timestamp']) : false;

            return ($rightTimestamp ?: 0) <=> ($leftTimestamp ?: 0);
        });

        return array_map(
            static fn (array $post): array => [
                'permalink' => $post['permalink'],
                'images' => $post['images'],
            ],
            array_slice($posts, 0, self::POST_LIMIT),
        );
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

        if ($this->instagramAuth->credentials() === null) {
            throw new UnexpectedValueException('Instagram credentials are unavailable for image retrieval.');
        }

        $downloadedImages = $this->instagramImageDownloader->download($imagesToDownload);
        $watermarkedImages = [];

        foreach ($downloadedImages as $imageKey => $contents) {
            $watermarkedImages[$imageKey] = $this->instagramImageWatermarker->watermark($contents);
        }

        foreach ($watermarkedImages as $imageKey => $contents) {
            if (! $storage->put($this->imagePath($imageKey), $contents)) {
                throw new UnexpectedValueException('Instagram image could not be written to storage.');
            }
        }
    }

    private function imagePath(string $imageKey): string
    {
        return 'instagram-images/'.$imageKey.'.jpg';
    }
}
