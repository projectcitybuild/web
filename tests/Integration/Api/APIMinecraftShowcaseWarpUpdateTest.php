<?php

namespace Tests\Integration\Api;

use App\Models\ShowcaseWarp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\IntegrationTestCase;

class APIMinecraftShowcaseWarpUpdateTest extends IntegrationTestCase
{
    use RefreshDatabase;

    private ShowcaseWarp $existingWarp;

    private function endpoint(?string $name = null): string
    {
        return 'api/v2/minecraft/showcase/'.($name ?: $this->existingWarp->name);
    }

    private function validData(): array
    {
        return [
            'name' => 'new_name',
            'title' => 'new_title',
            'description' => 'new_description',
            'creators' => 'new_creators',
            'location_world' => 'new_world',
            'location_x' => 1,
            'location_y' => 2,
            'location_z' => 3,
            'location_pitch' => 4.0,
            'location_yaw' => 5.0,
            'built_at' => now()->addDays(5)->timestamp,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->existingWarp = ShowcaseWarp::factory()->create();
    }

    public function test_requires_scope()
    {
        $this->postJson(uri: $this->endpoint(), data: $this->validData())
            ->assertUnauthorized();

        $this->withServerToken()
            ->postJson(uri: $this->endpoint(), data: $this->validData())
            ->assertSuccessful();
    }

    public function test_updates_warp()
    {
        $this->withServerToken()
            ->postJson(uri: $this->endpoint(), data: [
                'location_world' => 'new_world',
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
                'id' => $this->existingWarp->getKey(),
                'location_world' => 'new_world',
                'location_x' => 1,
                'location_y' => 2,
                'location_z' => 3,
                'location_pitch' => 4.0,
                'location_yaw' => 5.0,
            ],
        );
    }
    public function test_returns_resource()
    {
        $this->withServerToken()
            ->postJson(uri: $this->endpoint(), data: [
                'location_world' => 'new_world',
                'location_x' => 1,
                'location_y' => 2,
                'location_z' => 3,
                'location_pitch' => 4.0,
                'location_yaw' => 5.0,
            ])
            ->assertJson([
                'id' => $this->existingWarp->getKey(),
                'location_world' => 'new_world',
                'location_x' => 1,
                'location_y' => 2,
                'location_z' => 3,
                'location_pitch' => 4.0,
                'location_yaw' => 5.0,
            ])
            ->assertSuccessful();
    }

    public function test_throws_404_if_not_found()
    {
        $this->withServerToken()
            ->postJson(uri: $this->endpoint('missing_warp'))
            ->assertNotFound();
    }
}
