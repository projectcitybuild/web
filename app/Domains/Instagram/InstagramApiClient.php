<?php

namespace App\Domains\Instagram;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
use UnexpectedValueException;

final class InstagramApiClient
{
    private const MAX_IMAGE_BYTES = 10485760;

    public function refreshAccessToken(string $accessToken): string
    {
        $response = Http::connectTimeout(3)
            ->timeout(5)
            ->get('https://graph.instagram.com/refresh_access_token', [
                'grant_type' => 'ig_refresh_token',
                'access_token' => $accessToken,
            ])
            ->throw();

        $refreshedAccessToken = $response->json('access_token');
        $expiresIn = $response->json('expires_in');

        if (
            ! is_string($refreshedAccessToken)
            || $refreshedAccessToken === ''
            || ! is_int($expiresIn)
            || $expiresIn <= 0
        ) {
            throw new UnexpectedValueException('Instagram returned an invalid access token refresh response.');
        }

        return $refreshedAccessToken;
    }

    /**
     * @return array<int|string, mixed>
     */
    public function fetchMedia(string $apiVersion, string $userId, string $accessToken): array
    {
        $response = Http::connectTimeout(3)
            ->timeout(5)
            ->get(
                'https://graph.instagram.com/'.$apiVersion.'/'.$userId.'/media',
                [
                    'fields' => 'alt_text,media_type,media_url,permalink,timestamp,children{media_type,media_url}',
                    'limit' => 50,
                    'access_token' => $accessToken,
                ],
            )
            ->throw();

        $mediaItems = $response->json('data');

        if (! is_array($mediaItems)) {
            throw new UnexpectedValueException('Instagram Graph API returned an invalid media list.');
        }

        return $mediaItems;
    }

    /**
     * @param  array<string, array{imageUrl: string, imageKey: string, altText: string}>  $images
     * @return array<string, string>
     */
    public function downloadImages(array $images): array
    {
        $responses = Http::pool(static function (Pool $pool) use ($images): array {
            $requests = [];

            foreach ($images as $imageKey => $image) {
                $requests[$imageKey] = $pool->as($imageKey)
                    ->connectTimeout(3)
                    ->timeout(5)
                    ->withOptions(['allow_redirects' => false])
                    ->get($image['imageUrl']);
            }

            return $requests;
        });

        $downloadedImages = [];

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

            $downloadedImages[$imageKey] = $contents;
        }

        return $downloadedImages;
    }
}
