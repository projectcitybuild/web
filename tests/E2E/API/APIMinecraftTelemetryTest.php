<?php

namespace Tests\E2E\API;

use Domain\ServerTokens\ScopeKey;
use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Models\Eloquent\MinecraftPlayerAlias;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\E2ETestCase;

class APIMinecraftTelemetryTest extends E2ETestCase
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
        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: ['uuid' => 'uuid', 'alias' => 'alias'])
            ->assertUnauthorized();

        $this->authoriseTokenFor(ScopeKey::TELEMETRY);

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: ['uuid' => 'uuid', 'alias' => 'alias'])
            ->assertOk();
    }

    public function test_validates_input()
    {
        $this->authoriseTokenFor(ScopeKey::TELEMETRY);

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
            'last_synced_at' => $this->now->copy()->subWeek()
        ]);

        $this->authoriseTokenFor(ScopeKey::TELEMETRY);

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: ['uuid' => 'uuid', 'alias' => 'alias'])
            ->assertOk();

        $this->assertDatabaseHas(
            table: MinecraftPlayer::getTableName(),
            data: [
                'player_minecraft_id' => $player->getKey(),
                'uuid' => 'uuid',
                'last_synced_at' => $this->now,
            ],
        );
        $this->assertDatabaseHas(
            table: MinecraftPlayerAlias::getTableName(),
            data: [
                'alias' => 'alias',
            ],
        );
    }
}
