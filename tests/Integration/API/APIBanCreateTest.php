<?php

namespace Tests\Integration\API;

use Carbon\Carbon;
use Domain\ServerTokens\ScopeKey;
use Entities\Models\Eloquent\GameBan;
use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Models\PlayerIdentifierType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\IntegrationTestCase;

class APIBanCreateTest extends IntegrationTestCase
{
    use RefreshDatabase;

    private const ENDPOINT = 'api/v2/bans/ban';

    protected function setUp(): void
    {
        parent::setUp();

        $this->createServerToken();
    }

    private function validData(): array
    {
        return [
            'banned_player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
            'banned_player_id' => 'uuid1',
            'banned_player_alias' => 'alias1',
            'banner_player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
            'banner_player_id' => 'uuid2',
            'banner_player_alias' => 'alias2',
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

    public function test_creates_permanent_ban()
    {
        $this->authoriseTokenFor(ScopeKey::BAN_UPDATE);

        $player1 = MinecraftPlayer::factory()->create(['uuid' => 'uuid1']);
        $player2 = MinecraftPlayer::factory()->create(['uuid' => 'uuid2']);

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: [
                'banned_player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
                'banned_player_id' => $player1->uuid,
                'banned_player_alias' => 'alias1',
                'banner_player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
                'banner_player_id' => $player2->uuid,
                'banner_player_alias' => 'alias2',
                'reason' => 'reason',
            ])
            ->assertSuccessful();

        $this->assertDatabaseHas(
            table: GameBan::getTableName(),
            data: [
                'server_id' => $this->token->server->getKey(),
                'banned_player_id' => $player1->getKey(),
                'banned_alias_at_time' => 'alias1',
                'staff_player_id' => $player2->getKey(),
                'reason' => 'reason',
                'is_active' => true,
                'is_global_ban' => true,
                'expires_at' => null,
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ],
        );
    }

    public function test_creates_temporary_ban()
    {
        $this->authoriseTokenFor(ScopeKey::BAN_UPDATE);

        $expiryDate = Carbon::now()->addMonth();

        $player1 = MinecraftPlayer::factory()->create(['uuid' => 'uuid1']);
        $player2 = MinecraftPlayer::factory()->create(['uuid' => 'uuid2']);

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: [
                'banned_player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
                'banned_player_id' => $player1->uuid,
                'banned_player_alias' => 'alias1',
                'banner_player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
                'banner_player_id' => $player2->uuid,
                'banner_player_alias' => 'alias2',
                'reason' => 'reason',
                'expires_at' => $expiryDate->timestamp,
            ])
            ->assertSuccessful();

        $this->assertDatabaseHas(
            table: GameBan::getTableName(),
            data: [
                'server_id' => $this->token->server->getKey(),
                'banned_player_id' => $player1->getKey(),
                'banned_alias_at_time' => 'alias1',
                'staff_player_id' => $player2->getKey(),
                'reason' => 'reason',
                'is_active' => true,
                'is_global_ban' => true,
                'expires_at' => $expiryDate,
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ],
        );
    }

    public function test_permanent_ban_throws_exception_if_already_permanent_banned()
    {
        $this->authoriseTokenFor(ScopeKey::BAN_UPDATE);

        $player1 = MinecraftPlayer::factory()->create(['uuid' => 'uuid1']);
        $player2 = MinecraftPlayer::factory()->create(['uuid' => 'uuid2']);

        GameBan::factory()
            ->active()
            ->bannedPlayer($player1)
            ->create();

        $this->assertDatabaseCount(table: GameBan::getTableName(), count: 1);

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: [
                'banned_player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
                'banned_player_id' => $player1->uuid,
                'banned_player_alias' => 'alias1',
                'banner_player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
                'banner_player_id' => $player2->uuid,
                'banner_player_alias' => 'alias2',
                'reason' => 'reason',
            ])
            ->assertJson([
                'error' => [
                    'id' => 'player_already_banned',
                    'title' => '',
                    'detail' => 'Player is already permanently banned',
                    'status' => 400,
                ],
            ]);

        $this->assertDatabaseCount(table: GameBan::getTableName(), count: 1);
    }

    public function test_permanent_ban_throws_exception_if_already_temp_banned()
    {
        $this->authoriseTokenFor(ScopeKey::BAN_UPDATE);

        $player1 = MinecraftPlayer::factory()->create(['uuid' => 'uuid1']);
        $player2 = MinecraftPlayer::factory()->create(['uuid' => 'uuid2']);

        GameBan::factory()
            ->active()
            ->temporary()
            ->bannedPlayer($player1)
            ->create();

        $this->assertDatabaseCount(table: GameBan::getTableName(), count: 1);

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: [
                'banned_player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
                'banned_player_id' => $player1->uuid,
                'banned_player_alias' => 'alias1',
                'banner_player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
                'banner_player_id' => $player2->uuid,
                'banner_player_alias' => 'alias2',
                'reason' => 'reason',
            ])
            ->assertJson([
                'error' => [
                    'id' => 'player_already_temp_banned',
                    'title' => '',
                    'detail' => 'Player is already banned temporarily',
                    'status' => 400,
                ],
            ]);

        $this->assertDatabaseCount(table: GameBan::getTableName(), count: 1);
    }
}
