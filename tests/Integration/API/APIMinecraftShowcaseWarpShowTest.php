<?php

namespace Tests\Integration\API;

use App\Models\ShowcaseWarp;
use Domain\ServerTokens\ScopeKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\IntegrationTestCase;

class APIMinecraftShowcaseWarpShowTest extends IntegrationTestCase
{
    use RefreshDatabase;

    private ShowcaseWarp $existingWarp;

    private function endpoint(?string $name = null): string
    {
        return 'api/v2/minecraft/showcase-warps/'.$name ?: $this->existingWarp->name;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->existingWarp = ShowcaseWarp::factory()->create();
        $this->createServerToken();
    }

    public function test_requires_scope()
    {
        $this->withAuthorizationServerToken()
            ->getJson(uri: $this->endpoint())
            ->assertForbidden();

        $this->authoriseTokenFor(ScopeKey::SHOWCASE_WARPS_SHOW);

        $this->withAuthorizationServerToken()
            ->getJson(uri: $this->endpoint())
            ->assertSuccessful();
    }

    public function test_returns_warp()
    {
        $this->authoriseTokenFor(ScopeKey::SHOWCASE_WARPS_SHOW);

        $this->withAuthorizationServerToken()
            ->getJson(uri: $this->endpoint())
            ->assertJson([
                'data' => [
                    [
                        'id' => $this->existingWarp->getKey(),
                        'name' => $this->existingWarp->name,
                        'title' => $this->existingWarp->title,
                        'description' => $this->existingWarp->description,
                        'creators' => $this->existingWarp->creators,
                        'location_world' => $this->existingWarp->location_world,
                        'location_x' => $this->existingWarp->location_x,
                        'location_y' => $this->existingWarp->location_y,
                        'location_z' => $this->existingWarp->location_z,
                        'location_pitch' => $this->existingWarp->location_pitch,
                        'location_yaw' => $this->existingWarp->location_yaw,
                        'built_at' => $this->existingWarp->built_at->timestamp,
                        'created_at' => $this->existingWarp->created_at->timestamp,
                        'updated_at' => $this->existingWarp->updated_at->timestamp,
                    ],
                ],
            ])
            ->assertSuccessful();
    }

    public function test_throws_404_if_not_found()
    {
        $this->authoriseTokenFor(ScopeKey::SHOWCASE_WARPS_SHOW);

        $this->withAuthorizationServerToken()
            ->getJson(uri: $this->endpoint('missing_warp'))
            ->assertNotFound();
    }
}
