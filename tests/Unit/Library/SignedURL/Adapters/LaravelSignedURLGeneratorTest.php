<?php

namespace Tests\Unit\Library\SignedURL\Adapters;

use App\Core\Domains\SignedURL\Adapters\LaravelSignedURLGenerator;
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
        $now = $this->setTestNow();

        $generator = new LaravelSignedURLGenerator();
        $url = $generator->makeTemporary(
            routeName: self::ROUTE_NAME,
            expiresAt: $now->addDay(),
            parameters: ['key' => 'value'],
        );

        $this->assertTrue(str_contains(haystack: $url, needle: 'key=value'));
        $this->get($url)->assertSuccessful();
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
