<?php

namespace Tests\Unit\App\Core\Support\Laravel\SignedURL\Adapters;

use App\Core\Support\Laravel\SignedURL\Adapters\LaravelSignedURLGenerator;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class LaravelSignedURLGeneratorTest extends TestCase
{
    private const ROUTE_NAME = 'test-signed';

    public function test_make_generates_valid_url()
    {
        $generator = new LaravelSignedURLGenerator();
        $url = $generator->make(
            routeName: self::ROUTE_NAME,
            parameters: ['key' => 'value'],
        );

        $this->assertTrue(str_contains(haystack: $url, needle: 'key=value'));
        $this->get($url)->assertSuccessful();
    }

    public function test_generates_temporary_valid_url()
    {
        $this->freezeTime(function (Carbon $now) {
            $generator = new LaravelSignedURLGenerator();
            $url = $generator->makeTemporary(
                routeName: self::ROUTE_NAME,
                expiresAt: $now->addDay(),
                parameters: ['key' => 'value'],
            );

            $this->assertTrue(str_contains(haystack: $url, needle: 'key=value'));
            $this->get($url)->assertSuccessful();
        });
    }

    public function test_temporary_url_expires()
    {
        $generator = new LaravelSignedURLGenerator();
        $url = $generator->makeTemporary(
            routeName: self::ROUTE_NAME,
            expiresAt: now()->addDays(-1),
            parameters: ['key' => 'value'],
        );

        $this->get($url)->assertStatus(403);
    }
}
