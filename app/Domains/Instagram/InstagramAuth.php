<?php

namespace App\Domains\Instagram;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use UnexpectedValueException;

final class InstagramAuth
{
    private const TOKEN_REFRESH_SAFETY_MARGIN_SECONDS = 604800;

    public function __construct(private InstagramApiClient $instagramApiClient) {}

    public function isConfigured(): bool
    {
        $identifiers = $this->configurationIdentifiers();

        if ($identifiers === null) {
            return false;
        }

        $accessToken = config('services.instagram.access_token');

        return Cache::has($this->accessTokenCacheKey($identifiers['userId']))
            || (is_string($accessToken) && $accessToken !== '');
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
        $cacheKey = $this->accessTokenCacheKey($userId);

        if (Cache::has($cacheKey)) {
            $cachedToken = $this->decryptCachedToken($userId);

            if (
                $cachedToken === null
                || ! is_int($cachedToken['expiresAt'])
                || $cachedToken['expiresAt'] <= now()->timestamp + self::TOKEN_REFRESH_SAFETY_MARGIN_SECONDS
            ) {
                return null;
            }

            $accessToken = $cachedToken['accessToken'];
        } else {
            $accessToken = config('services.instagram.access_token');
        }

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
        $identifiers = $this->configurationIdentifiers();

        if ($identifiers === null) {
            throw new UnexpectedValueException('Instagram access token refresh is not configured.');
        }

        $userId = $identifiers['userId'];
        $cacheKey = $this->accessTokenCacheKey($userId);
        $accessToken = $this->refreshableAccessToken($userId, $cacheKey);

        if ($accessToken === null) {
            throw new UnexpectedValueException('Instagram access token refresh is not configured.');
        }

        try {
            $refreshedAccessToken = $this->instagramApiClient->refreshAccessToken($accessToken);
        } catch (UnexpectedValueException $exception) {
            Log::error('Instagram access token refresh failed.', [
                'exception' => $exception::class,
            ]);

            throw $exception;
        }

        Cache::forever(
            $cacheKey,
            [
                'accessToken' => Crypt::encryptString($refreshedAccessToken->accessToken),
                'expiresAt' => now()->addSeconds($refreshedAccessToken->expiresIn)->timestamp,
            ],
        );
    }

    /**
     * @return array{accessToken: string, expiresAt: int|null}|null
     */
    private function decryptCachedToken(string $userId): ?array
    {
        $cachedToken = Cache::get($this->accessTokenCacheKey($userId));

        if (is_string($cachedToken)) {
            try {
                $accessToken = Crypt::decryptString($cachedToken);
            } catch (DecryptException) {
                Log::warning('Instagram cached access token could not be decrypted.');

                return null;
            }

            if ($accessToken === '') {
                Log::warning('Instagram cached access token is empty.');

                return null;
            }

            return [
                'accessToken' => $accessToken,
                'expiresAt' => null,
            ];
        }

        if (
            ! is_array($cachedToken)
            || ! is_string($cachedToken['accessToken'] ?? null)
            || (! is_int($cachedToken['expiresAt'] ?? null) && ($cachedToken['expiresAt'] ?? null) !== null)
        ) {
            Log::warning('Instagram cached access token has an invalid format.');

            return null;
        }

        try {
            $accessToken = Crypt::decryptString($cachedToken['accessToken']);
        } catch (DecryptException) {
            Log::warning('Instagram cached access token could not be decrypted.');

            return null;
        }

        if ($accessToken === '') {
            Log::warning('Instagram cached access token is empty.');

            return null;
        }

        return [
            'accessToken' => $accessToken,
            'expiresAt' => $cachedToken['expiresAt'] ?? null,
        ];
    }

    private function refreshableAccessToken(string $userId, string $cacheKey): ?string
    {
        if (! Cache::has($cacheKey)) {
            $configuredAccessToken = config('services.instagram.access_token');

            return is_string($configuredAccessToken) && $configuredAccessToken !== ''
                ? $configuredAccessToken
                : null;
        }

        $cachedToken = $this->decryptCachedToken($userId);

        if ($cachedToken === null) {
            throw new UnexpectedValueException('Instagram cached access token is invalid or expired.');
        }

        if ($cachedToken['expiresAt'] !== null && $cachedToken['expiresAt'] <= now()->timestamp) {
            Log::warning('Instagram cached access token has expired.');

            throw new UnexpectedValueException('Instagram cached access token is invalid or expired.');
        }

        return $cachedToken['accessToken'];
    }

    private function accessTokenCacheKey(string $userId): string
    {
        return 'instagram.access-token.'.$userId;
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
