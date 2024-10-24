<?php

namespace Tests\Integration\Api;

use App\Models\ShowcaseWarp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\IntegrationTestCase;

class APIMinecraftShowcaseWarpIndexTest extends IntegrationTestCase
{
    use RefreshDatabase;

    private const ENDPOINT = 'api/v2/minecraft/showcase';

    public function test_requires_scope()
    {
        $this->getJson(uri: self::ENDPOINT)
            ->assertUnauthorized();

        $this->withServerToken()
            ->getJson(uri: self::ENDPOINT)
            ->assertSuccessful();
    }

    public function test_returns_all_warps()
    {
        $warp1 = ShowcaseWarp::factory()->create(['name' => 'warp1']);
        $warp2 = ShowcaseWarp::factory()->create(['name' => 'warp2']);

        $this->withServerToken()
            ->getJson(uri: self::ENDPOINT)
            ->assertJson([
                $warp1,
                $warp2,
            ])
            ->assertSuccessful();
    }

    public function test_returns_empty_list()
    {
        $this->withServerToken()
            ->getJson(uri: self::ENDPOINT)
            ->assertJson([])
            ->assertSuccessful();
    }
}
