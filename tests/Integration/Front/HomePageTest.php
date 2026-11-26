<?php

use App\Domains\Instagram\InstagramAuth;
use App\Domains\Instagram\InstagramMediaFeed;
use App\Domains\Instagram\Jobs\RefreshInstagramAccessTokenJob;
use App\Domains\Instagram\Jobs\RefreshInstagramMediaFeedJob;
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

    $sourceImage = imagecreatetruecolor(640, 480);

    if (! $sourceImage instanceof GdImage) {
        throw new RuntimeException('Test JPEG could not be created.');
    }

    $sourceColor = imagecolorallocate($sourceImage, 60, 90, 120);

    if ($sourceColor === false) {
        throw new RuntimeException('Test JPEG color could not be allocated.');
    }

    imagefill($sourceImage, 0, 0, $sourceColor);
    ob_start();

    try {
        if (! imagejpeg($sourceImage, null, 100)) {
            throw new RuntimeException('Test JPEG could not be encoded.');
        }

        $jpegContents = ob_get_contents();

        if (! is_string($jpegContents)) {
            throw new RuntimeException('Test JPEG contents could not be read.');
        }
    } finally {
        ob_end_clean();
        unset($sourceImage);
    }

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
                        'data' => array_merge(
                            [[
                                'media_type' => 'VIDEO',
                                'media_url' => 'https://scontent.cdninstagram.com/carousel-video.mp4',
                            ]],
                            array_map(static fn (int $index): array => [
                                'media_type' => 'IMAGE',
                                'media_url' => 'https://instagram.ffuk'.($index % 2 + 2).'-1.fna.fbcdn.net/carousel-image-'.$index.'.jpg',
                                'alt_text' => 'Carousel photo '.$index,
                            ], range(1, 11)),
                        ),
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
            $jpegContents,
            200,
            ['Content-Type' => 'image/jpeg'],
        ),
        'https://instagram.ffuk3-1.fna.fbcdn.net/*' => Http::response(
            $jpegContents,
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
        ->assertSee('View all 10 photos from this Instagram post', false)
        ->assertSee('data-image-alt="Carousel photo 2"', false)
        ->assertSee('aria-labelledby="instagram-carousel-heading"', false)
        ->assertSee('<h2 id="instagram-carousel-heading" class="sr-only">Instagram photo carousel</h2>', false)
        ->assertSee('data-instagram-carousel-retry', false)
        ->assertSee(route('front.instagram.image', [
            'imageKey' => hash('sha256', 'https://www.instagram.com/p/CAROUSEL123/#1'),
        ]), false)
        ->assertDontSee('video.mp4', false)
        ->assertDontSee('fbcdn.net', false)
        ->assertSee('https://www.instagram.com/p/LATEST123/', false);

    $latestImageKey = hash('sha256', 'https://www.instagram.com/p/LATEST123/');

    $latestImagePath = 'instagram-images/'.$latestImageKey.'.jpg';
    Storage::disk('local')->assertExists($latestImagePath);
    Http::assertSentCount(4);

    $storedImageContents = Storage::disk('local')->get($latestImagePath);
    expect($storedImageContents)->not->toBe($jpegContents);

    $watermarkedImage = imagecreatefromstring($storedImageContents);
    $originalImage = imagecreatefromstring($jpegContents);

    if (! $watermarkedImage instanceof GdImage || ! $originalImage instanceof GdImage) {
        throw new RuntimeException('Test JPEG could not be decoded.');
    }

    expect(imagecolorat($watermarkedImage, imagesx($watermarkedImage) - 12, imagesy($watermarkedImage) - 12))
        ->not->toBe(imagecolorat($originalImage, imagesx($originalImage) - 12, imagesy($originalImage) - 12));

    $hasWhiteWatermarkPixel = false;

    for ($y = imagesy($watermarkedImage) - 40; $y < imagesy($watermarkedImage) - 8; $y++) {
        for ($x = imagesx($watermarkedImage) - 300; $x < imagesx($watermarkedImage) - 8; $x++) {
            $pixel = imagecolorat($watermarkedImage, $x, $y);

            if (
                (($pixel >> 16) & 0xFF) > 200
                && (($pixel >> 8) & 0xFF) > 200
                && ($pixel & 0xFF) > 200
            ) {
                $hasWhiteWatermarkPixel = true;

                break 2;
            }
        }
    }

    expect($hasWhiteWatermarkPixel)->toBeTrue();

    unset($watermarkedImage, $originalImage);

    $imageResponse = $this->get(route('front.instagram.image', ['imageKey' => $latestImageKey]));

    $imageResponse
        ->assertOk()
        ->assertHeader('Content-Type', 'image/jpeg')
        ->assertHeader('Cache-Control', 'max-age=3600, public')
        ->assertHeader('Content-Disposition', 'inline')
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertStreamed()
        ->assertStreamedContent($storedImageContents);

    $secondCarouselImageKey = hash('sha256', 'https://www.instagram.com/p/CAROUSEL123/#1');
    $secondCarouselImagePath = 'instagram-images/'.$secondCarouselImageKey.'.jpg';

    Storage::disk('local')->assertMissing($secondCarouselImagePath);

    $this->get(route('front.instagram.image', ['imageKey' => $secondCarouselImageKey]))
        ->assertOk()
        ->assertHeader('Content-Type', 'image/jpeg')
        ->assertStreamedContent(Storage::disk('local')->get($secondCarouselImagePath));

    Storage::disk('local')->assertExists($secondCarouselImagePath);

    Http::assertSentCount(5);
    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && parse_url($request->url(), PHP_URL_HOST) === 'graph.instagram.com'
        && parse_url($request->url(), PHP_URL_PATH) === '/v27.0/987654321/media'
        && $request->header('Authorization') === ['Bearer instagram-test-token']
        && ! str_contains($request->url(), 'instagram-test-token')
        && str_contains($request['fields'], 'children{alt_text,media_type,media_url}')
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

    (new RefreshInstagramAccessTokenJob)->handle(app(InstagramAuth::class));
    (new RefreshInstagramMediaFeedJob)->handle(app(InstagramMediaFeed::class));

    Http::assertSent(fn (Request $request): bool => parse_url($request->url(), PHP_URL_PATH) === '/refresh_access_token'
        && $request['grant_type'] === 'ig_refresh_token'
        && $request->header('Authorization') === ['Bearer instagram-test-token']
        && ! str_contains($request->url(), 'instagram-test-token'));
    Http::assertSent(fn (Request $request): bool => parse_url($request->url(), PHP_URL_PATH) === '/v27.0/987654321/media'
        && $request->header('Authorization') === ['Bearer instagram-refreshed-token']
        && ! str_contains($request->url(), 'instagram-refreshed-token'));
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

test('carousel image cache misses fail safely when cached credentials are invalid', function () {
    config([
        'filesystems.default' => 'local',
        'services.instagram.user_id' => '987654321',
        'services.instagram.access_token' => 'instagram-configured-token',
        'services.instagram.api_version' => 'v27.0',
    ]);
    Storage::fake('local');
    Cache::forever('instagram.access-token.987654321', 'not-encrypted');

    $permalink = 'https://www.instagram.com/p/CACHEMISS1/';
    $imageKey = hash('sha256', $permalink.'#1');
    Cache::forever('instagram.cached-images.v3.v27.0.987654321', [
        'refreshedAt' => '2026-11-25T12:00:00+00:00',
        'posts' => [[
            'permalink' => $permalink,
            'images' => [
                [
                    'imageUrl' => 'https://scontent.cdninstagram.com/cover.jpg',
                    'imageKey' => hash('sha256', $permalink),
                    'altText' => 'Cover photo',
                ],
                [
                    'imageUrl' => 'https://scontent.cdninstagram.com/carousel.jpg',
                    'imageKey' => $imageKey,
                    'altText' => 'Carousel photo',
                ],
            ],
        ]],
    ]);
    Http::preventStrayRequests();

    $this->get(route('front.instagram.image', ['imageKey' => $imageKey]))
        ->assertNotFound();

    Http::assertSentCount(0);
});

test('Instagram image delivery uses the configured filesystem disk', function () {
    config(['filesystems.default' => 'backup']);
    Storage::fake('backup');
    Http::preventStrayRequests();

    $imageKey = hash('sha256', 'configured-storage-image');
    $imageContents = "\xFF\xD8\xFFcached-jpeg";
    Storage::disk('backup')->put('instagram-images/'.$imageKey.'.jpg', $imageContents);

    $this->get(route('front.instagram.image', ['imageKey' => $imageKey]))
        ->assertOk()
        ->assertHeader('Content-Type', 'image/jpeg')
        ->assertStreamedContent($imageContents);

    Http::assertSentCount(0);
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
