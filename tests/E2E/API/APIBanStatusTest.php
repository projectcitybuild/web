<?php

namespace Tests\E2E\API;

use Domain\ServerTokens\ScopeKey;
use Entities\Models\Eloquent\GameBan;
use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Models\Eloquent\Server;
use Entities\Models\PlayerIdentifierType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\E2ETestCase;

class APIBanStatusTest extends E2ETestCase
{
    use RefreshDatabase;

    private const ENDPOINT = 'api/v2/bans/status';

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

        $ban = GameBan::factory()
            ->active()
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
                    'is_active' => true,
                    'expires_at' => null,
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

        $ban = GameBan::factory()
            ->active()
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
                    'is_active' => true,
                    'expires_at' => $ban->expires_at->timestamp,
                ],
            ])
            ->assertSuccessful();
    }

    public function test_not_banned()
    {
        $this->authoriseTokenFor(ScopeKey::BAN_LOOKUP);

        $player = MinecraftPlayer::factory()->create(['uuid' => 'uuid1']);

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
