<?php

namespace Tests\Integration\Api;

use App\Models\ShowcaseWarp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\IntegrationTestCase;

class APIMinecraftShowcaseWarpStoreTest extends IntegrationTestCase
{
    use RefreshDatabase;

    private const ENDPOINT = 'api/v2/minecraft/showcase';

    private function validData(): array
    {
        return [
            'name' => 'test_warp',
            'location_world' => 'creative',
            'location_x' => 1,
            'location_y' => 2,
            'location_z' => 3,
            'location_pitch' => 4.0,
            'location_yaw' => 5.0,
        ];
    }

    public function test_requires_scope()
    {
        $this->postJson(uri: self::ENDPOINT, data: $this->validData())
            ->assertUnauthorized();

        $this->withServerToken()
            ->postJson(uri: self::ENDPOINT, data: $this->validData())
            ->assertSuccessful();
    }

    public function test_creates_warp_without_all_fields()
    {
        $this->withServerToken()
            ->postJson(uri: self::ENDPOINT, data: [
                'name' => 'test_warp',
                'location_world' => 'creative',
                'location_x' => 1,
                'location_y' => 2,
                'location_z' => 3,
                'location_pitch' => 4.0,
                'location_yaw' => 5.0,
            ])
            ->assertSuccessful();

        $this->assertDatabaseHas(
            table: ShowcaseWarp::tableName(),
            data: [
                'name' => 'test_warp',
                'title' => null,
                'description' => null,
                'creators' => null,
                'location_world' => 'creative',
                'location_x' => 1,
                'location_y' => 2,
                'location_z' => 3,
                'location_pitch' => 4.0,
                'location_yaw' => 5.0,
                'built_at' => null,
            ],
        );
    }

    public function test_creates_warp_with_all_fields()
    {
        $this->withServerToken()
            ->postJson(uri: self::ENDPOINT, data: [
                'name' => 'test_warp',
                'title' => 'title',
                'description' => 'description',
                'creators' => 'creators',
                'location_world' => 'creative',
                'location_x' => 1,
                'location_y' => 2,
                'location_z' => 3,
                'location_pitch' => 4.0,
                'location_yaw' => 5.0,
                'built_at' => 1663839908,
            ])
            ->assertSuccessful();

        $this->assertDatabaseHas(
            table: ShowcaseWarp::tableName(),
            data: [
                'name' => 'test_warp',
                'title' => 'title',
                'description' => 'description',
                'creators' => 'creators',
                'location_world' => 'creative',
                'location_x' => 1,
                'location_y' => 2,
                'location_z' => 3,
                'location_pitch' => 4.0,
                'location_yaw' => 5.0,
                'built_at' => '2022-09-22 09:45:08',
            ],
        );
    }

    public function test_returns_resource()
    {
        $this->withServerToken()
            ->postJson(uri: self::ENDPOINT, data: [
                'name' => 'test_warp',
                'title' => 'title',
                'description' => 'description',
                'creators' => 'creators',
                'location_world' => 'creative',
                'location_x' => 1,
                'location_y' => 2,
                'location_z' => 3,
                'location_pitch' => 4.0,
                'location_yaw' => 5.0,
                'built_at' => 1663839908,
            ])
            ->assertJson([
                'name' => 'test_warp',
                'title' => 'title',
                'description' => 'description',
                'creators' => 'creators',
                'location_world' => 'creative',
                'location_x' => 1,
                'location_y' => 2,
                'location_z' => 3,
                'location_pitch' => 4.0,
                'location_yaw' => 5.0,
                'built_at' => 1663839908,
            ])
            ->assertSuccessful();
    }

    public function test_cannot_reuse_name()
    {
        ShowcaseWarp::factory()->create(['name' => 'test_warp']);

        $this->withServerToken()
            ->postJson(uri: self::ENDPOINT, data: [
                'name' => 'test_warp',
                'location_world' => 'creative',
                'location_x' => 1,
                'location_y' => 2,
                'location_z' => 3,
                'location_pitch' => 4.0,
                'location_yaw' => 5.0,
            ])
            ->assertInvalid(['name']);
    }
}
