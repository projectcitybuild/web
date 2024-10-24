<?php

namespace Tests\Integration\Api;

use App\Models\ShowcaseWarp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\IntegrationTestCase;

class APIMinecraftShowcaseWarpShowTest extends IntegrationTestCase
{
    use RefreshDatabase;

    private ShowcaseWarp $existingWarp;

    private function endpoint(?string $name = null): string
    {
        return 'api/v2/minecraft/showcase/'.$name ?: $this->existingWarp->name;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->existingWarp = ShowcaseWarp::factory()->create();
    }

    public function test_requires_scope()
    {
        $this->getJson(uri: $this->endpoint())
            ->assertUnauthorized();

        $this->withServerToken()
            ->getJson(uri: $this->endpoint())
            ->assertSuccessful();
    }

    public function test_returns_warp()
    {
        $this->withServerToken()
            ->getJson(uri: $this->endpoint())
            ->assertJson([$this->existingWarp])
            ->assertSuccessful();
    }

    public function test_throws_404_if_not_found()
    {
        $this->withServerToken()
            ->getJson(uri: $this->endpoint('missing_warp'))
            ->assertNotFound();
    }
}
