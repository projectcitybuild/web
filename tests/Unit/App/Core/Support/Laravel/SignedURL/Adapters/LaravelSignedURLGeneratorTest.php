<?php

namespace Tests\Unit\App\Core\Support\Laravel\SignedURL\Adapters;

use App\Core\Support\Laravel\SignedURL\Adapters\LaravelSignedURLGenerator;
use Illuminate\Support\Carbon;

it('generates a valid signed URL', function () {
    $routeName = 'test-signed';
    $generator = new LaravelSignedURLGenerator;
    $url = $generator->make(
        routeName: $routeName,
        parameters: ['key' => 'value'],
    );

    $this->assertTrue(str_contains(haystack: $url, needle: 'key=value'));
    $this->get($url)->assertSuccessful();
});

it('generates a valid temporary signed URL', function () {
    $routeName = 'test-signed';
    $this->freezeTime(function (Carbon $now) use ($routeName) {
        $generator = new LaravelSignedURLGenerator;
        $url = $generator->makeTemporary(
            routeName: $routeName,
            expiresAt: $now->addDay(),
            parameters: ['key' => 'value'],
        );

        $this->assertTrue(str_contains(haystack: $url, needle: 'key=value'));
        $this->get($url)->assertSuccessful();
    });
});

it('rejects an expired temporary URL', function () {
    $routeName = 'test-signed';
    $generator = new LaravelSignedURLGenerator;
    $url = $generator->makeTemporary(
        routeName: $routeName,
        expiresAt: now()->addDays(-1),
        parameters: ['key' => 'value'],
    );

    $this->get($url)->assertStatus(403);
});
