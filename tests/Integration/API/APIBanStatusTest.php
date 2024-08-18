<?php

namespace Tests\Integration\API;

use App\Domains\ServerTokens\ScopeKey;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use App\Models\Server;
use Entities\Models\PlayerIdentifierType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\IntegrationTestCase;

class APIBanStatusTest extends IntegrationTestCase
{
    use RefreshDatabase;

    private const ENDPOINT = 'api/v2/bans/player/status';

    protected function setUp(): void
    {
        parent::setUp();

        $this->createServerToken();
    }

    private function validData(): array
    {
        return [
            'player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
            'player_id' => 'uuid1',
        ];
    }

    public function test_requires_scope()
    {
        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: $this->validData())
            ->assertForbidden();

        $this->authoriseTokenFor(ScopeKey::BAN_LOOKUP);

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: $this->validData())
            ->assertSuccessful();
    }

    public function test_permanently_banned()
    {
        $this->authoriseTokenFor(ScopeKey::BAN_LOOKUP);

        $player1 = MinecraftPlayer::factory()->create(['uuid' => 'uuid1']);
        $player2 = MinecraftPlayer::factory()->create(['uuid' => 'uuid2']);
        $server = Server::factory()->create();

        $ban = GamePlayerBan::factory()
            ->bannedPlayer($player1)
            ->bannedBy($player2)
            ->server($server)
            ->create();

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: [
                'player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
                'player_id' => $player1->uuid,
            ])
            ->assertJson([
                'data' => [
                    'id' => $ban->getKey(),
                    'server_id' => $server->getKey(),
                    'banned_player_id' => $player1->getKey(),
                    'banner_player_id' => $player2->getKey(),
                    'expires_at' => null,
                    'unbanned_at' => null,
                    'unbanner_player_id' => null,
                    'unban_type' => null,
                ],
            ])
            ->assertSuccessful();
    }

    public function test_temporarily_banned()
    {
        $this->authoriseTokenFor(ScopeKey::BAN_LOOKUP);

        $player1 = MinecraftPlayer::factory()->create(['uuid' => 'uuid1']);
        $player2 = MinecraftPlayer::factory()->create(['uuid' => 'uuid2']);
        $server = Server::factory()->create();

        $ban = GamePlayerBan::factory()
            ->bannedPlayer($player1)
            ->bannedBy($player2)
            ->server($server)
            ->temporary()
            ->create();

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: [
                'player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
                'player_id' => $player1->uuid,
            ])
            ->assertJson([
                'data' => [
                    'id' => $ban->getKey(),
                    'server_id' => $server->getKey(),
                    'banned_player_id' => $player1->getKey(),
                    'banner_player_id' => $player2->getKey(),
                    'expires_at' => $ban->expires_at->timestamp,
                    'unbanned_at' => null,
                    'unbanner_player_id' => null,
                    'unban_type' => null,
                ],
            ])
            ->assertSuccessful();
    }

    public function test_not_banned_when_no_ban()
    {
        $this->authoriseTokenFor(ScopeKey::BAN_LOOKUP);

        $player = MinecraftPlayer::factory()->create();

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: [
                'player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
                'player_id' => $player->uuid,
            ])
            ->assertJson([
                'data' => [],
            ])
            ->assertSuccessful();
    }

    public function test_not_banned_when_ban_expired()
    {
        $this->authoriseTokenFor(ScopeKey::BAN_LOOKUP);

        $player = MinecraftPlayer::factory()->create();

        GamePlayerBan::factory()
            ->bannedPlayer($player)
            ->bannedBy(MinecraftPlayer::factory())
            ->expired()
            ->create();

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: [
                'player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
                'player_id' => $player->uuid,
            ])
            ->assertJson([
                'data' => [],
            ])
            ->assertSuccessful();
    }

    public function test_not_banned_when_manually_unbanned()
    {
        $this->authoriseTokenFor(ScopeKey::BAN_LOOKUP);

        $player = MinecraftPlayer::factory()->create();

        GamePlayerBan::factory()
            ->bannedPlayer($player)
            ->bannedBy(MinecraftPlayer::factory())
            ->inactive()
            ->create();

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: [
                'player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
                'player_id' => $player->uuid,
            ])
            ->assertJson([
                'data' => [],
            ])
            ->assertSuccessful();
    }

    public function test_not_banned_when_ban_expired_but_missing_unban_date()
    {
        $this->authoriseTokenFor(ScopeKey::BAN_LOOKUP);

        $player = MinecraftPlayer::factory()->create();

        GamePlayerBan::factory()
            ->bannedPlayer($player)
            ->bannedBy(MinecraftPlayer::factory())
            ->create([
                'expires_at' => now()->subDay(),
                'unbanned_at' => null,
            ]);

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: [
                'player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
                'player_id' => $player->uuid,
            ])
            ->assertJson([
                'data' => [],
            ])
            ->assertSuccessful();
    }
}
