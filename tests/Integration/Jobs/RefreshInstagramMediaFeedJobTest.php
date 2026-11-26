<?php

use App\Domains\Instagram\InstagramMediaFeed;
use App\Domains\Instagram\Jobs\RefreshInstagramMediaFeedJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

it('fails when Instagram media refresh is not configured', function () {
    config([
        'services.instagram.user_id' => null,
        'services.instagram.access_token' => null,
        'services.instagram.api_version' => 'v27.0',
    ]);

    expect(fn () => (new RefreshInstagramMediaFeedJob)->handle(app(InstagramMediaFeed::class)))
        ->toThrow(UnexpectedValueException::class, 'Instagram media feed is not configured.');
});

it('schedules Instagram media refresh every thirty minutes on one scheduler', function () {
    $event = collect(app(Schedule::class)->events())
        ->first(fn ($event): bool => $event->attributes['admin_title'] === 'Refresh Instagram media feed');

    expect($event)->not->toBeNull()
        ->and($event->attributes['admin_subtitle'])->toBe('instagram.media-feed.refresh')
        ->and($event->expression)->toBe('*/30 * * * *')
        ->and($event->withoutOverlapping)->toBeTrue()
        ->and($event->onOneServer)->toBeTrue();
});

it('fetches multiple media pages until the latest sixteen eligible posts are cached', function () {
    $this->travelTo('2026-11-26 00:00:00');
    config([
        'filesystems.default' => 'local',
        'services.instagram.user_id' => '987654321',
        'services.instagram.access_token' => 'instagram-test-token',
        'services.instagram.api_version' => 'v27.0',
    ]);
    Storage::fake('local');
    Cache::forget('instagram.access-token.987654321');
    Cache::forget('instagram.cached-images.v3.v27.0.987654321');
    Http::preventStrayRequests();

    $makeImagePost = static fn (int $postId, string $timestamp): array => [
        'media_type' => 'IMAGE',
        'media_url' => 'https://scontent.cdninstagram.com/photo-'.$postId.'.jpg',
        'permalink' => 'https://www.instagram.com/p/POST'.$postId.'/',
        'timestamp' => $timestamp,
    ];
    $firstPage = array_map(
        static fn (int $postId): array => $makeImagePost($postId, '2026-11-24T12:00:'.str_pad((string) $postId, 2, '0', STR_PAD_LEFT).'+0000'),
        range(1, 8),
    );
    $secondPage = array_map(
        static fn (int $postId): array => $makeImagePost($postId, '2026-11-25T12:00:'.str_pad((string) $postId, 2, '0', STR_PAD_LEFT).'+0000'),
        range(9, 16),
    );
    $firstPage[] = [
        'media_type' => 'VIDEO',
        'media_url' => 'https://scontent.cdninstagram.com/video.mp4',
        'permalink' => 'https://www.instagram.com/p/VIDEO1/',
        'timestamp' => '2026-11-24T13:00:00+0000',
    ];
    $firstPage[] = [
        'media_type' => 'IMAGE',
        'media_url' => 'https://untrusted.example/image.jpg',
        'permalink' => 'https://www.instagram.com/p/UNTRUSTED1/',
        'timestamp' => '2026-11-24T14:00:00+0000',
    ];

    foreach (array_merge($firstPage, $secondPage) as $post) {
        if ($post['media_type'] === 'IMAGE' && str_contains($post['media_url'], 'cdninstagram.com')) {
            $imageKey = hash('sha256', $post['permalink']);
            Storage::disk('local')->put('instagram-images/'.$imageKey.'.jpg', 'cached-image');
        }
    }

    Http::fake([
        'https://graph.instagram.com/v27.0/987654321/media*' => static function (Request $request) use ($firstPage, $secondPage) {
            if (($request->data()['after'] ?? null) === 'page-two') {
                return Http::response(['data' => $secondPage]);
            }

            return Http::response([
                'data' => $firstPage,
                'paging' => [
                    'cursors' => ['after' => 'page-two'],
                    'next' => 'https://graph.instagram.com/v27.0/987654321/media?after=page-two',
                ],
            ]);
        },
    ]);

    (new RefreshInstagramMediaFeedJob)->handle(app(InstagramMediaFeed::class));

    $posts = app(InstagramMediaFeed::class)->latestImages();

    expect($posts)->toHaveCount(16)
        ->and($posts[0]['permalink'])->toBe('https://www.instagram.com/p/POST16/')
        ->and($posts[15]['permalink'])->toBe('https://www.instagram.com/p/POST1/')
        ->and(app(InstagramMediaFeed::class)->lastSuccessfulRefreshAt())->toBe(now()->toIso8601String());

    Http::assertSentCount(2);
    Http::assertSent(fn (Request $request): bool => ($request->data()['after'] ?? null) === 'page-two');
    expect(array_column($posts, 'permalink'))
        ->not->toContain('https://www.instagram.com/p/UNTRUSTED1/');
});

it('preserves the last successful feed snapshot when Instagram returns an invalid media page', function () {
    config([
        'filesystems.default' => 'local',
        'services.instagram.user_id' => '987654321',
        'services.instagram.access_token' => 'instagram-test-token',
        'services.instagram.api_version' => 'v27.0',
    ]);
    Storage::fake('local');
    Cache::forget('instagram.access-token.987654321');
    $snapshot = [
        'refreshedAt' => '2026-11-25T12:00:00+00:00',
        'posts' => [[
            'permalink' => 'https://www.instagram.com/p/PREVIOUS1/',
            'images' => [[
                'imageUrl' => 'https://scontent.cdninstagram.com/previous.jpg',
                'imageKey' => hash('sha256', 'https://www.instagram.com/p/PREVIOUS1/'),
                'altText' => 'Previous feed image',
            ]],
        ]],
    ];
    Cache::forever('instagram.cached-images.v3.v27.0.987654321', $snapshot);
    Http::preventStrayRequests();
    Http::fake([
        'https://graph.instagram.com/v27.0/987654321/media*' => Http::response(['data' => 'invalid']),
    ]);

    expect(fn () => (new RefreshInstagramMediaFeedJob)->handle(app(InstagramMediaFeed::class)))
        ->toThrow(UnexpectedValueException::class, 'invalid media list');

    expect(Cache::get('instagram.cached-images.v3.v27.0.987654321'))->toBe($snapshot)
        ->and(app(InstagramMediaFeed::class)->latestImages()[0]['permalink'])
        ->toBe('https://www.instagram.com/p/PREVIOUS1/');
});

it('preserves the existing feed when a downloaded image has an invalid MIME type or JPEG signature', function (
    string $contentType,
    string $contents,
) {
    config([
        'filesystems.default' => 'local',
        'services.instagram.user_id' => '987654321',
        'services.instagram.access_token' => 'instagram-test-token',
        'services.instagram.api_version' => 'v27.0',
    ]);
    Storage::fake('local');
    Cache::forget('instagram.access-token.987654321');
    $snapshot = [
        'refreshedAt' => '2026-11-25T12:00:00+00:00',
        'posts' => [],
    ];
    Cache::forever('instagram.cached-images.v3.v27.0.987654321', $snapshot);
    Http::preventStrayRequests();
    Http::fake([
        'https://graph.instagram.com/v27.0/987654321/media*' => Http::response([
            'data' => [[
                'media_type' => 'IMAGE',
                'media_url' => 'https://scontent.cdninstagram.com/invalid-image.jpg',
                'permalink' => 'https://www.instagram.com/p/INVALIDIMAGE1/',
                'timestamp' => '2026-11-25T13:00:00+0000',
            ]],
        ]),
        'https://scontent.cdninstagram.com/*' => Http::response(
            $contents,
            200,
            ['Content-Type' => $contentType],
        ),
    ]);

    expect(fn () => (new RefreshInstagramMediaFeedJob)->handle(app(InstagramMediaFeed::class)))
        ->toThrow(UnexpectedValueException::class);

    expect(Cache::get('instagram.cached-images.v3.v27.0.987654321'))->toBe($snapshot);
    Storage::disk('local')->assertMissing(
        'instagram-images/'.hash('sha256', 'https://www.instagram.com/p/INVALIDIMAGE1/').'.jpg',
    );
})->with([
    'invalid MIME type' => ['text/html', '<html>not an image</html>'],
    'invalid JPEG signature' => ['image/jpeg', '<html>not a JPEG</html>'],
]);

it('stops reading an oversized image at the configured size limit', function () {
    config([
        'filesystems.default' => 'local',
        'services.instagram.user_id' => '987654321',
        'services.instagram.access_token' => 'instagram-test-token',
        'services.instagram.api_version' => 'v27.0',
    ]);
    Storage::fake('local');
    Cache::forget('instagram.access-token.987654321');
    $snapshot = [
        'refreshedAt' => '2026-11-25T12:00:00+00:00',
        'posts' => [],
    ];
    Cache::forever('instagram.cached-images.v3.v27.0.987654321', $snapshot);
    $oversizedImage = "\xFF\xD8\xFF".str_repeat('x', 10485758);
    Http::preventStrayRequests();
    Http::fake([
        'https://graph.instagram.com/v27.0/987654321/media*' => Http::response([
            'data' => [[
                'media_type' => 'IMAGE',
                'media_url' => 'https://scontent.cdninstagram.com/oversized-image.jpg',
                'permalink' => 'https://www.instagram.com/p/OVERSIZED1/',
                'timestamp' => '2026-11-25T13:00:00+0000',
            ]],
        ]),
        'https://scontent.cdninstagram.com/*' => Http::response(
            $oversizedImage,
            200,
            [
                'Content-Type' => 'image/jpeg',
                'Content-Length' => '3',
            ],
        ),
    ]);

    expect(fn () => (new RefreshInstagramMediaFeedJob)->handle(app(InstagramMediaFeed::class)))
        ->toThrow(UnexpectedValueException::class, 'size limit');

    expect(Cache::get('instagram.cached-images.v3.v27.0.987654321'))->toBe($snapshot);
    Storage::disk('local')->assertMissing(
        'instagram-images/'.hash('sha256', 'https://www.instagram.com/p/OVERSIZED1/').'.jpg',
    );
});

it('does not publish a refreshed feed when an image cannot be watermarked', function () {
    config([
        'filesystems.default' => 'local',
        'services.instagram.user_id' => '987654321',
        'services.instagram.access_token' => 'instagram-test-token',
        'services.instagram.api_version' => 'v27.0',
    ]);
    Storage::fake('local');
    Cache::forget('instagram.access-token.987654321');
    $snapshot = [
        'refreshedAt' => '2026-11-25T12:00:00+00:00',
        'posts' => [],
    ];
    Cache::forever('instagram.cached-images.v3.v27.0.987654321', $snapshot);

    $smallImage = imagecreatetruecolor(1, 1);

    if (! $smallImage instanceof GdImage) {
        throw new RuntimeException('Test JPEG could not be created.');
    }

    ob_start();

    try {
        if (! imagejpeg($smallImage)) {
            throw new RuntimeException('Test JPEG could not be encoded.');
        }

        $smallJpeg = ob_get_contents();
    } finally {
        ob_end_clean();
        imagedestroy($smallImage);
    }

    if (! is_string($smallJpeg)) {
        throw new RuntimeException('Test JPEG contents could not be read.');
    }

    Http::preventStrayRequests();
    Http::fake([
        'https://graph.instagram.com/v27.0/987654321/media*' => Http::response([
            'data' => [[
                'media_type' => 'IMAGE',
                'media_url' => 'https://scontent.cdninstagram.com/small-image.jpg',
                'permalink' => 'https://www.instagram.com/p/SMALLIMAGE1/',
                'timestamp' => '2026-11-25T13:00:00+0000',
            ]],
        ]),
        'https://scontent.cdninstagram.com/*' => Http::response(
            $smallJpeg,
            200,
            ['Content-Type' => 'image/jpeg'],
        ),
    ]);

    expect(fn () => (new RefreshInstagramMediaFeedJob)->handle(app(InstagramMediaFeed::class)))
        ->toThrow(UnexpectedValueException::class, 'too small to watermark');

    expect(Cache::get('instagram.cached-images.v3.v27.0.987654321'))->toBe($snapshot);
    Storage::disk('local')->assertMissing(
        'instagram-images/'.hash('sha256', 'https://www.instagram.com/p/SMALLIMAGE1/').'.jpg',
    );
});
