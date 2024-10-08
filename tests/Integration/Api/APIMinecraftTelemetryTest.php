<?php

namespace Tests\Integration\API;

use App\Models\MinecraftPlayer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\IntegrationTestCase;

class APIMinecraftTelemetryTest extends IntegrationTestCase
{
    use RefreshDatabase;

    private const ENDPOINT = 'api/v2/minecraft/telemetry/seen';

    protected function setUp(): void
    {
        parent::setUp();

        $this->createServerToken();
    }

    public function test_requires_scope()
    {
        $this->postJson(uri: self::ENDPOINT, data: ['uuid' => 'uuid', 'alias' => 'alias'])
            ->assertUnauthorized();

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: ['uuid' => 'uuid', 'alias' => 'alias'])
            ->assertOk();
    }

    public function test_validates_input()
    {
        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: ['alias' => 'alias'])
            ->assertStatus(400);

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: ['uuid' => 'uuid'])
            ->assertStatus(400);

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: ['uuid' => 'uuid', 'alias' => 'alias'])
            ->assertOk();
    }

    public function test_updates_last_seen_date()
    {
        $player = MinecraftPlayer::factory()->create([
            'uuid' => 'uuid',
            'last_seen_at' => $this->now->copy()->subWeek(),
        ]);

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: ['uuid' => 'uuid', 'alias' => 'alias'])
            ->assertOk();

        $this->assertDatabaseHas(
            table: MinecraftPlayer::tableName(),
            data: [
                'player_minecraft_id' => $player->getKey(),
                'uuid' => 'uuid',
                'alias' => 'alias',
                'last_seen_at' => $this->now,
            ],
        );
    }
}
