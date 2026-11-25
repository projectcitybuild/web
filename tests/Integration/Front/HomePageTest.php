<?php

use App\Core\Domains\Instagram\InstagramMediaFeed;
use App\Core\Domains\Instagram\Jobs\RefreshInstagramAccessTokenJob;
use App\Core\Domains\Instagram\Jobs\RefreshInstagramMediaFeedJob;
use App\Models\Account;
use App\Models\MinecraftPlayer;
use App\Models\MinecraftPlayerSession;
use App\Models\Role;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

test('homepage shows the latest Instagram photo posts', function () {
    config([
        'filesystems.default' => 'local',
        'services.instagram.user_id' => '987654321',
        'services.instagram.access_token' => 'instagram-test-token',
        'services.instagram.api_version' => 'v27.0',
    ]);

    Cache::forget('instagram.access-token.987654321');
    Cache::forget('instagram.cached-images.v3.v27.0.987654321');
    Storage::fake('local');
    Http::preventStrayRequests();
    Http::fake([
        'https://graph.instagram.com/v27.0/987654321/media*' => Http::response([
            'data' => [
                [
                    'media_type' => 'IMAGE',
                    'media_url' => 'https://instagram.ffuk2-1.fna.fbcdn.net/older-image.jpg',
                    'permalink' => 'https://www.instagram.com/p/OLDER123/',
                    'caption' => 'An older build',
                    'timestamp' => '2026-11-23T12:00:00+0000',
                ],
                [
                    'media_type' => 'VIDEO',
                    'media_url' => 'https://scontent.cdninstagram.com/video.mp4',
                    'permalink' => 'https://www.instagram.com/p/VIDEO123/',
                    'timestamp' => '2026-11-24T13:00:00+0000',
                ],
                [
                    'media_type' => 'CAROUSEL_ALBUM',
                    'media_url' => null,
                    'permalink' => 'https://www.instagram.com/p/CAROUSEL123/',
                    'caption' => 'A city build carousel',
                    'timestamp' => '2026-11-24T12:00:00+0000',
                    'children' => [
                        'data' => [
                            [
                                'media_type' => 'VIDEO',
                                'media_url' => 'https://scontent.cdninstagram.com/carousel-video.mp4',
                            ],
                            [
                                'media_type' => 'IMAGE',
                                'media_url' => 'https://instagram.ffuk2-1.fna.fbcdn.net/carousel-image.jpg',
                            ],
                            [
                                'media_type' => 'IMAGE',
                                'media_url' => 'https://instagram.ffuk3-1.fna.fbcdn.net/carousel-image-2.jpg',
                            ],
                        ],
                    ],
                ],
                [
                    'media_type' => 'IMAGE',
                    'media_url' => 'https://instagram.ffuk3-1.fna.fbcdn.net/latest-image.jpg',
                    'permalink' => 'https://www.instagram.com/p/LATEST123/',
                    'alt_text' => 'The latest city skyline',
                    'timestamp' => '2026-11-24T14:00:00+0000',
                ],
            ],
        ]),
        'https://instagram.ffuk2-1.fna.fbcdn.net/*' => Http::response(
            "\xFF\xD8\xFF\xD9",
            200,
            ['Content-Type' => 'image/jpeg'],
        ),
        'https://instagram.ffuk3-1.fna.fbcdn.net/*' => Http::response(
            "\xFF\xD8\xFF\xD9",
            200,
            ['Content-Type' => 'image/jpeg'],
        ),
    ]);

    (new RefreshInstagramMediaFeedJob)->handle(app(InstagramMediaFeed::class));

    Http::assertSentCount(4);

    $response = $this->get(route('front.home'));

    $response->assertOk()
        ->assertSeeInOrder([
            route('front.instagram.image', ['imageKey' => hash('sha256', 'https://www.instagram.com/p/LATEST123/')]),
            route('front.instagram.image', ['imageKey' => hash('sha256', 'https://www.instagram.com/p/CAROUSEL123/')]),
            route('front.instagram.image', ['imageKey' => hash('sha256', 'https://www.instagram.com/p/OLDER123/')]),
        ], false)
        ->assertSee('The latest city skyline', false)
        ->assertSee('View all 2 photos from this Instagram post', false)
        ->assertSee(route('front.instagram.image', [
            'imageKey' => hash('sha256', 'https://www.instagram.com/p/CAROUSEL123/#1'),
        ]), false)
        ->assertDontSee('video.mp4', false)
        ->assertDontSee('fbcdn.net', false)
        ->assertSee('https://www.instagram.com/p/LATEST123/', false);

    $latestImageKey = hash('sha256', 'https://www.instagram.com/p/LATEST123/');

    Storage::disk('local')->assertExists('instagram-images/'.$latestImageKey.'.jpg');
    Http::assertSentCount(4);

    $imageResponse = $this->get(route('front.instagram.image', ['imageKey' => $latestImageKey]));

    $imageResponse
        ->assertOk()
        ->assertHeader('Content-Type', 'image/jpeg')
        ->assertStreamed()
        ->assertStreamedContent("\xFF\xD8\xFF\xD9");

    $secondCarouselImageKey = hash('sha256', 'https://www.instagram.com/p/CAROUSEL123/#1');

    Storage::disk('local')->assertMissing('instagram-images/'.$secondCarouselImageKey.'.jpg');

    $this->get(route('front.instagram.image', ['imageKey' => $secondCarouselImageKey]))
        ->assertOk()
        ->assertHeader('Content-Type', 'image/jpeg')
        ->assertStreamedContent("\xFF\xD8\xFF\xD9");

    Storage::disk('local')->assertExists('instagram-images/'.$secondCarouselImageKey.'.jpg');

    Http::assertSentCount(5);
    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && parse_url($request->url(), PHP_URL_HOST) === 'graph.instagram.com'
        && parse_url($request->url(), PHP_URL_PATH) === '/v27.0/987654321/media'
        && $request['access_token'] === 'instagram-test-token'
        && str_contains($request['fields'], 'children{media_type,media_url}')
        && ! str_contains($request['fields'], 'caption'));
});

test('Instagram access token job refreshes the token used by media fetches', function () {
    config([
        'services.instagram.user_id' => '987654321',
        'services.instagram.access_token' => 'instagram-test-token',
        'services.instagram.api_version' => 'v27.0',
    ]);

    Cache::forget('instagram.access-token.987654321');
    Cache::forget('instagram.cached-images.v3.v27.0.987654321');
    Http::preventStrayRequests();
    Http::fake([
        'https://graph.instagram.com/refresh_access_token*' => Http::response([
            'access_token' => 'instagram-refreshed-token',
            'expires_in' => 5184000,
            'token_type' => 'bearer',
        ]),
        'https://graph.instagram.com/v27.0/987654321/media*' => Http::response(['data' => []]),
    ]);

    (new RefreshInstagramAccessTokenJob)->handle(app(InstagramMediaFeed::class));
    (new RefreshInstagramMediaFeedJob)->handle(app(InstagramMediaFeed::class));

    Http::assertSent(fn (Request $request): bool => parse_url($request->url(), PHP_URL_PATH) === '/refresh_access_token'
        && $request['grant_type'] === 'ig_refresh_token'
        && $request['access_token'] === 'instagram-test-token');
    Http::assertSent(fn (Request $request): bool => parse_url($request->url(), PHP_URL_PATH) === '/v27.0/987654321/media'
        && $request['access_token'] === 'instagram-refreshed-token');
});

test('homepage does not fetch Instagram when no cached posts are available', function () {
    config([
        'services.instagram.user_id' => '987654321',
        'services.instagram.access_token' => 'instagram-test-token',
        'services.instagram.api_version' => 'v27.0',
    ]);

    Cache::forget('instagram.access-token.987654321');
    Cache::forget('instagram.cached-images.v3.v27.0.987654321');
    Http::preventStrayRequests();

    $this->get(route('front.home'))
        ->assertOk()
        ->assertSee('No recent Instagram images are available.')
        ->assertDontSee('instagram-test-token');

    Http::assertSentCount(0);
});

test('homepage does not cache Instagram responses that are not JPEG images', function () {
    config([
        'filesystems.default' => 'local',
        'services.instagram.user_id' => '987654321',
        'services.instagram.access_token' => 'instagram-test-token',
        'services.instagram.api_version' => 'v27.0',
    ]);

    Cache::forget('instagram.access-token.987654321');
    Cache::forget('instagram.cached-images.v3.v27.0.987654321');
    Storage::fake('local');
    Http::preventStrayRequests();
    Http::fake([
        'https://graph.instagram.com/v27.0/987654321/media*' => Http::response([
            'data' => [
                [
                    'media_type' => 'IMAGE',
                    'media_url' => 'https://scontent.cdninstagram.com/not-an-image.jpg',
                    'permalink' => 'https://www.instagram.com/p/INVALID123/',
                    'timestamp' => '2026-11-24T14:00:00+0000',
                ],
            ],
        ]),
        'https://scontent.cdninstagram.com/*' => Http::response(
            '<html>not an image</html>',
            200,
            ['Content-Type' => 'text/html'],
        ),
    ]);

    expect(fn () => (new RefreshInstagramMediaFeedJob)->handle(app(InstagramMediaFeed::class)))
        ->toThrow(UnexpectedValueException::class);

    $this->get(route('front.home'))
        ->assertOk()
        ->assertSee('No recent Instagram images are available.')
        ->assertDontSee('scontent.cdninstagram.com', false);

    Storage::disk('local')->assertMissing(
        'instagram-images/'.hash('sha256', 'https://www.instagram.com/p/INVALID123/').'.jpg',
    );
});

test('Instagram image endpoint rejects keys outside its hash format', function () {
    $this->get('/instagram-images/not-a-hash')
        ->assertNotFound();
});

test('homepage shows account roles for recent players', function () {
    $account = Account::factory()->create();
    $player = MinecraftPlayer::factory()->for($account)->create(['alias' => 'RolePlayer']);
    $role = Role::factory()->create([
        'name' => 'architect',
        'alias' => 'Architect',
    ]);

    $account->roles()->attach($role);

    MinecraftPlayerSession::query()->create([
        'player_id' => $player->id,
        'seconds' => 600,
        'starts_at' => now()->subHour(),
        'ends_at' => now()->subHour()->addMinutes(10),
    ]);

    $this->get(route('front.home'))
        ->assertOk()
        ->assertSee('RolePlayer')
        ->assertSee('Architect');
});

test('homepage shows unique players ordered by their most recent connection', function () {
    $this->travelTo('2026-11-24 12:00:00');

    $newestPlayer = MinecraftPlayer::factory()->create(['alias' => 'NewestPlayer']);
    $middlePlayer = MinecraftPlayer::factory()->create(['alias' => 'MiddlePlayer']);
    $oldestPlayer = MinecraftPlayer::factory()->create(['alias' => 'OldestPlayer']);

    MinecraftPlayerSession::query()->create([
        'player_id' => $newestPlayer->id,
        'seconds' => 600,
        'starts_at' => now()->subHour(),
        'ends_at' => now()->subHour()->addMinutes(10),
    ]);
    MinecraftPlayerSession::query()->create([
        'player_id' => $newestPlayer->id,
        'seconds' => 600,
        'starts_at' => now()->subHours(8),
        'ends_at' => now()->subHours(8)->addMinutes(10),
    ]);
    MinecraftPlayerSession::query()->create([
        'player_id' => $middlePlayer->id,
        'seconds' => 600,
        'starts_at' => now()->subHours(2),
        'ends_at' => now()->subHours(2)->addMinutes(10),
    ]);
    MinecraftPlayerSession::query()->create([
        'player_id' => $oldestPlayer->id,
        'seconds' => 600,
        'starts_at' => now()->subHours(6),
        'ends_at' => now()->subHours(6)->addMinutes(10),
    ]);

    $response = $this->get(route('front.home'));

    $response->assertOk()
        ->assertSeeInOrder([
            'NewestPlayer',
            '1 hour ago',
            'MiddlePlayer',
            '2 hours ago',
            'OldestPlayer',
            '6 hours ago',
        ])
        ->assertSee('https://minotar.net/helm/'.$newestPlayer->uuid.'/64', false)
        ->assertSee('https://minotar.net/helm/'.$middlePlayer->uuid.'/64', false)
        ->assertSee('https://minotar.net/helm/'.$oldestPlayer->uuid.'/64', false);

    expect(substr_count($response->getContent(), 'NewestPlayer'))->toBe(1);
});

test('signed-in homepage avatar uses compact dimensions', function () {
    $account = Account::factory()->create();
    $player = MinecraftPlayer::factory()->for($account)->create();

    $this->actingAs($account)
        ->get(route('front.home'))
        ->assertOk()
        ->assertSee('width="32" height="32"', false)
        ->assertSee('https://minotar.net/helm/'.$player->uuid, false);
});

test('homepage shows truncated version', function () {
    config([
        'app.version' => 'v123456789',
        'app.commit' => 'abcdef0123456789abcdef0123456789abcdef01',
    ]);

    $this->get(route('front.home'))
        ->assertOk()
        ->assertSee('v123456', false)
        ->assertSee('abcdef0', false)
        ->assertDontSee('v123456789', false);
});

test('homepage does not show unknown version', function () {
    $this->get(route('front.home'))
        ->assertOk()
        ->assertDontSee('unknown', false);
});
