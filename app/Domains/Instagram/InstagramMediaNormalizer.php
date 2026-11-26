<?php

namespace App\Domains\Instagram;

use UnexpectedValueException;

final class InstagramMediaNormalizer
{
    private const MAX_CAROUSEL_IMAGES = 10;

    /**
     * @param  array<string, mixed>  $response
     * @return array{
     *     posts: array<int, array{
     *         permalink: string,
     *         timestamp: string|null,
     *         images: array<int, array{imageUrl: string, imageKey: string, altText: string}>
     *     }>,
     *     nextCursor: string|null
     * }
     */
    public function normalizePage(array $response): array
    {
        $mediaItems = $response['data'] ?? null;

        if (! is_array($mediaItems) || ! array_is_list($mediaItems)) {
            throw new UnexpectedValueException('Instagram Graph API returned an invalid media list.');
        }

        $paging = $response['paging'] ?? [];

        if (! is_array($paging)) {
            throw new UnexpectedValueException('Instagram Graph API returned invalid pagination data.');
        }

        $nextPage = $paging['next'] ?? null;
        $nextCursor = null;

        if ($nextPage !== null && $nextPage !== '') {
            $cursors = $paging['cursors'] ?? null;
            $nextCursor = is_array($cursors) ? ($cursors['after'] ?? null) : null;

            if (! is_string($nextPage) || ! is_string($nextCursor) || $nextCursor === '') {
                throw new UnexpectedValueException('Instagram Graph API returned an invalid pagination cursor.');
            }
        }

        $posts = [];

        foreach ($mediaItems as $mediaItem) {
            if (! is_array($mediaItem)) {
                continue;
            }

            $permalink = $mediaItem['permalink'] ?? null;

            if (! $this->isInstagramPermalink($permalink)) {
                continue;
            }

            $mediaUrls = match ($mediaItem['media_type'] ?? null) {
                'IMAGE' => [[
                    'mediaUrl' => $mediaItem['media_url'] ?? null,
                    'altText' => $mediaItem['alt_text'] ?? null,
                ]],
                'CAROUSEL_ALBUM' => $this->carouselImages($mediaItem),
                default => [],
            };

            $validImages = [];

            foreach ($mediaUrls as $mediaUrl) {
                $imageUrl = $mediaUrl['mediaUrl'] ?? null;

                if (! InstagramImageDownloader::isAllowedUrl($imageUrl)) {
                    continue;
                }

                $validImages[] = [
                    'mediaUrl' => $imageUrl,
                    'altText' => $mediaUrl['altText'] ?? null,
                ];
            }

            if ($validImages === []) {
                continue;
            }

            $images = [];
            $postAltText = $mediaItem['alt_text'] ?? null;

            foreach ($validImages as $slideIndex => $image) {
                $childAltText = $image['altText'];
                $altText = is_string($childAltText) && trim($childAltText) !== ''
                    ? trim($childAltText)
                    : (is_string($postAltText) && trim($postAltText) !== ''
                        ? trim($postAltText)
                        : (count($validImages) > 1
                            ? 'Photo '.($slideIndex + 1).' of '.count($validImages).' from a Project City Build Instagram post'
                            : 'Project City Build Instagram post'));
                $imageKey = $slideIndex === 0
                    ? hash('sha256', $permalink)
                    : hash('sha256', $permalink.'#'.$slideIndex);

                $images[] = [
                    'imageUrl' => $image['mediaUrl'],
                    'imageKey' => $imageKey,
                    'altText' => $altText,
                ];
            }

            $posts[] = [
                'permalink' => $permalink,
                'timestamp' => is_string($mediaItem['timestamp'] ?? null) ? $mediaItem['timestamp'] : null,
                'images' => $images,
            ];
        }

        return [
            'posts' => $posts,
            'nextCursor' => $nextCursor,
        ];
    }

    /**
     * @param  array<string, mixed>  $mediaItem
     * @return array<int, array{mediaUrl: mixed, altText: mixed}>
     */
    private function carouselImages(array $mediaItem): array
    {
        $children = $mediaItem['children']['data'] ?? null;
        $images = [];

        if (is_array($children) && array_is_list($children)) {
            foreach ($children as $child) {
                if (is_array($child) && ($child['media_type'] ?? null) === 'IMAGE') {
                    $images[] = [
                        'mediaUrl' => $child['media_url'] ?? null,
                        'altText' => $child['alt_text'] ?? null,
                    ];
                }
            }
        }

        return array_slice($images, 0, self::MAX_CAROUSEL_IMAGES);
    }

    private function isInstagramPermalink(mixed $url): bool
    {
        if (! is_string($url) || filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        $parts = parse_url($url);

        return is_array($parts)
            && ($parts['scheme'] ?? null) === 'https'
            && strtolower(is_string($parts['host'] ?? null) ? $parts['host'] : '') === 'www.instagram.com'
            && ! isset($parts['user'])
            && ! isset($parts['pass'])
            && ! isset($parts['port']);
    }
}
