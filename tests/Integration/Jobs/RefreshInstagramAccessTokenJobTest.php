<?php

use App\Domains\Instagram\InstagramAuth;
use App\Domains\Instagram\Jobs\RefreshInstagramAccessTokenJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

it('fails when Instagram access token refresh is not configured', function () {
    config([
        'services.instagram.user_id' => null,
        'services.instagram.access_token' => null,
        'services.instagram.api_version' => 'v27.0',
    ]);

    expect(fn () => (new RefreshInstagramAccessTokenJob)->handle(app(InstagramAuth::class)))
        ->toThrow(UnexpectedValueException::class, 'Instagram access token refresh is not configured.');
});

it('schedules Instagram access token refresh weekly on one scheduler', function () {
    $event = collect(app(Schedule::class)->events())
        ->first(fn ($event): bool => $event->attributes['admin_title'] === 'Refresh Instagram access token');

    expect($event)->not->toBeNull()
        ->and($event->attributes['admin_subtitle'])->toBe('instagram.access-token.refresh')
        ->and($event->expression)->toBe('0 0 * * 0')
        ->and($event->timezone)->toBe('UTC')
        ->and($event->withoutOverlapping)->toBeTrue()
        ->and($event->onOneServer)->toBeTrue();
});

it('stores refreshed Instagram credentials encrypted with their expiration', function () {
    $this->travelTo('2026-11-26 00:00:00');
    config([
        'services.instagram.user_id' => '987654321',
        'services.instagram.access_token' => 'instagram-test-token',
        'services.instagram.api_version' => 'v27.0',
    ]);
    Http::preventStrayRequests();
    Http::fake([
        'https://graph.instagram.com/refresh_access_token*' => Http::response([
            'access_token' => 'instagram-refreshed-token',
            'expires_in' => 5184000,
            'token_type' => 'bearer',
        ]),
    ]);

    (new RefreshInstagramAccessTokenJob)->handle(app(InstagramAuth::class));

    $cachedToken = Cache::get('instagram.access-token.987654321');

    expect($cachedToken)->toBeArray()
        ->and($cachedToken['expiresAt'])->toBe(now()->addDays(60)->timestamp)
        ->and($cachedToken['accessToken'])->not->toBe('instagram-refreshed-token')
        ->and(Crypt::decryptString($cachedToken['accessToken']))->toBe('instagram-refreshed-token');
});

it('refreshes legacy encrypted Instagram credentials without using them for media requests', function () {
    config([
        'services.instagram.user_id' => '987654321',
        'services.instagram.access_token' => 'instagram-configured-token',
        'services.instagram.api_version' => 'v27.0',
    ]);
    Cache::forever(
        'instagram.access-token.987654321',
        Crypt::encryptString('instagram-legacy-token'),
    );
    Http::preventStrayRequests();
    Http::fake([
        'https://graph.instagram.com/refresh_access_token*' => Http::response([
            'access_token' => 'instagram-refreshed-token',
            'expires_in' => 5184000,
        ]),
    ]);

    expect(app(InstagramAuth::class)->credentials())->toBeNull();

    app(InstagramAuth::class)->refreshAccessToken();

    Http::assertSent(fn (Request $request): bool => $request->header('Authorization') === ['Bearer instagram-legacy-token']
        && ! str_contains($request->url(), 'instagram-legacy-token'));
    expect(Cache::get('instagram.access-token.987654321')['expiresAt'])->toBeGreaterThan(now()->timestamp);
});

it('rejects cached Instagram credentials inside the refresh safety margin', function () {
    config([
        'services.instagram.user_id' => '987654321',
        'services.instagram.access_token' => 'instagram-test-token',
        'services.instagram.api_version' => 'v27.0',
    ]);
    Cache::forever('instagram.access-token.987654321', [
        'accessToken' => Crypt::encryptString('instagram-expiring-token'),
        'expiresAt' => now()->addDays(6)->timestamp,
    ]);

    expect(app(InstagramAuth::class)->credentials())->toBeNull();
});

it('rejects expired or undecryptable cached Instagram credentials without using the configured token', function (
    array $cachedToken,
) {
    config([
        'services.instagram.user_id' => '987654321',
        'services.instagram.access_token' => 'instagram-configured-token',
        'services.instagram.api_version' => 'v27.0',
    ]);
    Cache::forever('instagram.access-token.987654321', $cachedToken);
    Http::preventStrayRequests();

    expect(app(InstagramAuth::class)->credentials())->toBeNull();
    expect(fn () => app(InstagramAuth::class)->refreshAccessToken())
        ->toThrow(UnexpectedValueException::class);

    Http::assertSentCount(0);
})->with([
    'expired token' => fn () => [
        'accessToken' => Crypt::encryptString('instagram-expired-token'),
        'expiresAt' => now()->subSecond()->timestamp,
    ],
    'undecryptable token' => fn () => [
        'accessToken' => 'not-encrypted',
        'expiresAt' => now()->addDays(30)->timestamp,
    ],
]);

it('retains the previous cached Instagram credentials when token refresh fails', function () {
    config([
        'services.instagram.user_id' => '987654321',
        'services.instagram.access_token' => 'instagram-test-token',
        'services.instagram.api_version' => 'v27.0',
    ]);
    $cachedToken = [
        'accessToken' => Crypt::encryptString('instagram-existing-token'),
        'expiresAt' => now()->addDays(30)->timestamp,
    ];
    Cache::forever('instagram.access-token.987654321', $cachedToken);
    Http::preventStrayRequests();
    Http::fake([
        'https://graph.instagram.com/refresh_access_token*' => Http::response([], 500),
    ]);

    expect(fn () => app(InstagramAuth::class)->refreshAccessToken())
        ->toThrow(UnexpectedValueException::class, 'status 500');

    expect(Cache::get('instagram.access-token.987654321'))->toBe($cachedToken);
});
