<?php

namespace Tests\Integration\API;

use App\Models\GameIPBan;
use App\Models\MinecraftPlayer;
use Domain\ServerTokens\ScopeKey;
use Entities\Models\PlayerIdentifierType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\IntegrationTestCase;

class APIBanIPCreateTest extends IntegrationTestCase
{
    use RefreshDatabase;

    private const ENDPOINT = 'api/v2/bans/ip/ban';

    protected function setUp(): void
    {
        parent::setUp();

        $this->createServerToken();
    }

    private function validData(): array
    {
        return [
            'ip_address' => '192.168.0.1',
            'banner_player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
            'banner_player_id' => 'uuid',
            'banner_player_alias' => 'alias',
            'reason' => 'reason',
        ];
    }

    public function test_requires_scope()
    {
        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: $this->validData())
            ->assertForbidden();

        $this->authoriseTokenFor(ScopeKey::BAN_UPDATE);

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: $this->validData())
            ->assertSuccessful();
    }

    public function test_creates_ban()
    {
        $this->authoriseTokenFor(ScopeKey::BAN_UPDATE);

        $player = MinecraftPlayer::factory()->create(['uuid' => 'uuid']);

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: [
                'ip_address' => '192.168.0.1',
                'banner_player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
                'banner_player_id' => $player->uuid,
                'banner_player_alias' => 'alias',
                'reason' => 'reason',
            ])
            ->assertSuccessful();

        $this->assertDatabaseHas(
            table: GameIPBan::getTableName(),
            data: [
                'ip_address' => '192.168.0.1',
                'banner_player_id' => $player->getKey(),
                'reason' => 'reason',
                'created_at' => $this->now,
                'updated_at' => $this->now,
                'unbanned_at' => null,
                'unbanner_player_id' => null,
                'unban_type' => null,
            ],
        );
    }

    public function test_throws_exception_if_already_banned()
    {
        $this->authoriseTokenFor(ScopeKey::BAN_UPDATE);

        $player = MinecraftPlayer::factory()->create(['uuid' => 'uuid']);

        $ban = GameIPBan::factory()
            ->bannedBy($player)
            ->create();

        $this->assertDatabaseCount(table: GameIPBan::getTableName(), count: 1);

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: [
                'ip_address' => $ban->ip_address,
                'banner_player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
                'banner_player_id' => $player->uuid,
                'banner_player_alias' => 'alias',
                'reason' => 'reason',
            ])
            ->assertJson([
                'error' => [
                    'id' => 'ip_already_banned',
                    'title' => '',
                    'detail' => 'IP address is already banned',
                    'status' => 400,
                ],
            ]);

        $this->assertDatabaseCount(table: GameIPBan::getTableName(), count: 1);
    }
}
