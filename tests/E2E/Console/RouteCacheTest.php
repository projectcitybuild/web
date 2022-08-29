<?php

namespace Tests\E2E\Console;

use Tests\TestCase;

class RouteCacheTest extends TestCase
{
    protected function tearDown(): void
    {
        $this->artisan('route:clear');

        parent::tearDown();
    }

    public function test_can_cache_routes()
    {
        $this->artisan('route:cache')
            ->assertExitCode(0);
    }
}
