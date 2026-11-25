<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

it('clears Instagram feed metadata and images while preserving the access token', function () {
    config([
        'filesystems.default' => 'local',
        'services.instagram.user_id' => '987654321',
        'services.instagram.api_version' => 'v27.0',
    ]);

    Storage::fake('local');

    $feedCacheKey = 'instagram.cached-images.v3.v27.0.987654321';
    $accessTokenCacheKey = 'instagram.access-token.987654321';
    $unrelatedCacheKey = 'unrelated.cache-key';

    Cache::forever($feedCacheKey, []);
    Cache::forever($accessTokenCacheKey, 'refreshed-token');
    Cache::forever($unrelatedCacheKey, 'keep-this-value');
    Storage::disk('local')->put('instagram-images/photo.jpg', 'cached-image');
    Storage::disk('local')->put('avatars/player.jpg', 'unrelated-image');

    $this->artisan('instagram:cache:clear')
        ->expectsOutput('Instagram media feed cache and images cleared. Cached access token was preserved.')
        ->assertExitCode(0);

    expect(Cache::has($feedCacheKey))->toBeFalse()
        ->and(Cache::get($accessTokenCacheKey))->toBe('refreshed-token')
        ->and(Cache::get($unrelatedCacheKey))->toBe('keep-this-value');

    Storage::disk('local')->assertDirectoryEmpty('instagram-images');
    Storage::disk('local')->assertExists('avatars/player.jpg');
});
