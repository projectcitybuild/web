<?php

namespace Tests\Integration\API;

use App\Core\Data\PlayerIdentifierType;
use App\Domains\Bans\UnbanType;
use App\Domains\ServerTokens\ScopeKey;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\IntegrationTestCase;

class APIUnbanCreateTest extends IntegrationTestCase
{
    use RefreshDatabase;

    private const ENDPOINT = 'api/v2/bans/player/unban';

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
            'unbanner_player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
            'unbanner_player_id' => 'uuid2',
        ];
    }

    public function test_requires_scope()
    {
        GamePlayerBan::factory()
            ->bannedPlayer(MinecraftPlayer::factory()->create(['uuid' => 'uuid1']))
            ->create();

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: $this->validData())
            ->assertForbidden();

        $this->authoriseTokenFor(ScopeKey::BAN_UPDATE);

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: $this->validData())
            ->assertSuccessful();
    }

    public function test_throws_exception_if_not_banned()
    {
        $this->authoriseTokenFor(ScopeKey::BAN_UPDATE);

        $player1 = MinecraftPlayer::factory()->create(['uuid' => 'uuid1']);
        $player2 = MinecraftPlayer::factory()->create(['uuid' => 'uuid2']);

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: [
                'banned_player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
                'banned_player_id' => $player1->uuid,
                'unbanner_player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
                'unbanner_player_id' => $player2->uuid,
                'reason' => 'reason',
            ])
            ->assertJson([
                'error' => [
                    'id' => 'player_not_banned',
                    'title' => '',
                    'detail' => 'This player is not currently banned',
                    'status' => 400,
                ],
            ]);
    }

    public function test_unbans_permanent_ban()
    {
        $this->authoriseTokenFor(ScopeKey::BAN_UPDATE);

        $player1 = MinecraftPlayer::factory()->create(['uuid' => 'uuid1']);
        $player2 = MinecraftPlayer::factory()->create(['uuid' => 'uuid2']);

        $ban = GamePlayerBan::factory()
            ->bannedPlayer($player1)
            ->create();

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: [
                'banned_player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
                'banned_player_id' => $player1->uuid,
                'unbanner_player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
                'unbanner_player_id' => $player2->uuid,
            ])
            ->assertSuccessful();

        $this->assertDatabaseHas(
            table: GamePlayerBan::getTableName(),
            data: [
                'id' => $ban->getKey(),
                'unbanned_at' => now(),
                'unbanner_player_id' => $player2->getKey(),
                'unban_type' => UnbanType::MANUAL->value,
            ],
        );
    }

    public function test_unbans_temporary_ban()
    {
        $this->authoriseTokenFor(ScopeKey::BAN_UPDATE);

        $player1 = MinecraftPlayer::factory()->create(['uuid' => 'uuid1']);
        $player2 = MinecraftPlayer::factory()->create(['uuid' => 'uuid2']);

        $ban = GamePlayerBan::factory()
            ->temporary()
            ->bannedPlayer($player1)
            ->create();

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: [
                'banned_player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
                'banned_player_id' => $player1->uuid,
                'unbanner_player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
                'unbanner_player_id' => $player2->uuid,
            ])
            ->assertSuccessful();

        $this->assertDatabaseHas(
            table: GamePlayerBan::getTableName(),
            data: [
                'id' => $ban->getKey(),
                'unbanned_at' => now(),
                'unbanner_player_id' => $player2->getKey(),
                'unban_type' => UnbanType::MANUAL->value,
            ],
        );
    }
}
