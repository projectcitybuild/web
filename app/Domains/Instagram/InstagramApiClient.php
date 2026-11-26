<?php

namespace App\Domains\Instagram;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use UnexpectedValueException;

final class InstagramApiClient
{
    public function refreshAccessToken(string $accessToken): InstagramAccessToken
    {
        $response = $this->get(
            'https://graph.instagram.com/refresh_access_token',
            ['grant_type' => 'ig_refresh_token'],
            $accessToken,
        );

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

        return new InstagramAccessToken($refreshedAccessToken, $expiresIn);
    }

    /**
     * @return array<string, mixed>
     */
    public function fetchMedia(
        string $apiVersion,
        string $userId,
        string $accessToken,
        ?string $after = null,
    ): array {
        $query = [
            'fields' => 'alt_text,media_type,media_url,permalink,timestamp,children{alt_text,media_type,media_url}',
            'limit' => 50,
        ];

        if ($after !== null) {
            $query['after'] = $after;
        }

        $response = $this->get(
            'https://graph.instagram.com/'.$apiVersion.'/'.$userId.'/media',
            $query,
            $accessToken,
        );
        $mediaResponse = $response->json();

        if (! is_array($mediaResponse)) {
            throw new UnexpectedValueException('Instagram Graph API returned an invalid media response.');
        }

        return $mediaResponse;
    }

    /**
     * @param  array<string, int|string>  $query
     */
    private function get(string $url, array $query, string $accessToken): Response
    {
        try {
            $response = Http::withToken($accessToken)
                ->connectTimeout(3)
                ->timeout(5)
                ->get($url, $query);
        } catch (ConnectionException) {
            throw new UnexpectedValueException('Instagram Graph API request could not be completed.');
        }

        if (! $response->successful()) {
            throw new UnexpectedValueException(
                'Instagram Graph API request failed with status '.$response->status().'.',
            );
        }

        return $response;
    }
}
