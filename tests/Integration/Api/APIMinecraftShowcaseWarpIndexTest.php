<?php

namespace Tests\Integration\API;

use App\Models\ShowcaseWarp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\IntegrationTestCase;

class APIMinecraftShowcaseWarpIndexTest extends IntegrationTestCase
{
    use RefreshDatabase;

    private const ENDPOINT = 'api/v2/minecraft/showcase-warps';

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
                'data' => [
                    [
                        'id' => $warp1->getKey(),
                        'name' => 'warp1',
                        'title' => $warp1->title,
                        'description' => $warp1->description,
                        'creators' => $warp1->creators,
                        'location_world' => $warp1->location_world,
                        'location_x' => $warp1->location_x,
                        'location_y' => $warp1->location_y,
                        'location_z' => $warp1->location_z,
                        'location_pitch' => $warp1->location_pitch,
                        'location_yaw' => $warp1->location_yaw,
                        'built_at' => $warp1->built_at->timestamp,
                        'created_at' => $warp1->created_at->timestamp,
                        'updated_at' => $warp1->updated_at->timestamp,
                    ],
                    [
                        'id' => $warp2->getKey(),
                        'name' => 'warp2',
                        'title' => $warp2->title,
                        'description' => $warp2->description,
                        'creators' => $warp2->creators,
                        'location_world' => $warp2->location_world,
                        'location_x' => $warp2->location_x,
                        'location_y' => $warp2->location_y,
                        'location_z' => $warp2->location_z,
                        'location_pitch' => $warp2->location_pitch,
                        'location_yaw' => $warp2->location_yaw,
                        'built_at' => $warp2->built_at->timestamp,
                        'created_at' => $warp2->created_at->timestamp,
                        'updated_at' => $warp2->updated_at->timestamp,
                    ],
                ],
            ])
            ->assertSuccessful();
    }

    public function test_returns_empty_list()
    {
        $this->withServerToken()
            ->getJson(uri: self::ENDPOINT)
            ->assertJson([
                'data' => [],
            ])
            ->assertSuccessful();
    }
}
