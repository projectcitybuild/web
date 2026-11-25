<?php

namespace App\Domains\Instagram;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use UnexpectedValueException;

final class InstagramAuth
{
    public function __construct(private InstagramApiClient $instagramApiClient) {}

    public function isConfigured(): bool
    {
        return $this->credentials() !== null;
    }

    /**
     * @return array{userId: string, accessToken: string, apiVersion: string}|null
     */
    public function credentials(): ?array
    {
        $identifiers = $this->configurationIdentifiers();

        if ($identifiers === null) {
            return null;
        }

        $userId = $identifiers['userId'];
        $cachedAccessToken = Cache::get('instagram.access-token.'.$userId);
        $accessToken = is_string($cachedAccessToken)
            ? Crypt::decryptString($cachedAccessToken)
            : config('services.instagram.access_token');

        if (! is_string($accessToken) || $accessToken === '') {
            return null;
        }

        return [
            'userId' => $userId,
            'accessToken' => $accessToken,
            'apiVersion' => $identifiers['apiVersion'],
        ];
    }

    public function refreshAccessToken(): void
    {
        $configuration = $this->credentials();

        if ($configuration === null) {
            throw new UnexpectedValueException('Instagram access token refresh is not configured.');
        }

        $accessToken = $this->instagramApiClient->refreshAccessToken($configuration['accessToken']);

        Cache::forever(
            'instagram.access-token.'.$configuration['userId'],
            Crypt::encryptString($accessToken),
        );
    }

    /**
     * @return array{userId: string, apiVersion: string}|null
     */
    public function configurationIdentifiers(): ?array
    {
        $userId = config('services.instagram.user_id');
        $apiVersion = config('services.instagram.api_version');

        if (
            ! is_string($userId)
            || ! ctype_digit($userId)
            || ! is_string($apiVersion)
            || preg_match('/^v\d+\.\d+$/D', $apiVersion) !== 1
        ) {
            return null;
        }

        return [
            'userId' => $userId,
            'apiVersion' => $apiVersion,
        ];
    }
}
