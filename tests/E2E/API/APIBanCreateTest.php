<?php

namespace Tests\E2E\API;

use Domain\ServerTokens\ScopeKey;
use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Models\Eloquent\MinecraftPlayerAlias;
use Entities\Models\PlayerIdentifierType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\E2ETestCase;

class APIBanCreateTest extends E2ETestCase
{
    use RefreshDatabase;

    private const ENDPOINT = 'api/v2/bans/ban';

    protected function setUp(): void
    {
        parent::setUp();

        $this->createServerToken();
    }

    private function validData(): array {
        return [
            'banned_player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
            'banned_player_id' => 'uuid1',
            'banner_player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
            'banner_player_id' => 'uuid2',
            'reason' => 'reason',
            'expires_at' => null,
        ];
    }

    public function test_requires_scope()
    {
        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: $this->validData())
            ->assertUnauthorized();

        $this->authoriseTokenFor(ScopeKey::BAN_UPDATE);

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: $this->validData())
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
            'last_seen_at' => $this->now->copy()->subWeek(),
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
                'last_seen_at' => $this->now,
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
