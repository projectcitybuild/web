<?php

namespace Tests\E2E\API;

use Domain\ServerTokens\ScopeKey;
use Entities\Models\Eloquent\GameBan;
use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Models\Eloquent\Server;
use Entities\Models\PlayerIdentifierType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\E2ETestCase;

class APIBanAllTest extends E2ETestCase
{
    use RefreshDatabase;

    private const ENDPOINT = 'api/v2/bans/all';

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

    public function test_shows_bans_and_unbans()
    {
        $this->authoriseTokenFor(ScopeKey::BAN_LOOKUP);

        $player1 = MinecraftPlayer::factory()->create(['uuid' => 'uuid1']);
        $player2 = MinecraftPlayer::factory()->create(['uuid' => 'uuid2']);
        $server = Server::factory()->create();

        $ban1 = GameBan::factory()
            ->active()
            ->bannedPlayer($player1)
            ->bannedBy($player2)
            ->server($server)
            ->create(['created_at' => now()->subDays(1)]);

        $ban2 = GameBan::factory()
            ->inactive()
            ->bannedPlayer($player1)
            ->bannedBy($player2)
            ->server($server)
            ->create(['created_at' => now()->subDays(2)]);  // Assertion gets confused without different created_at dates

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: [
                'player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
                'player_id' => $player1->uuid,
            ])
            ->assertJson([
                'data' => [
                    [
                        'id' => $ban1->getKey(),
                        'server_id' => $server->getKey(),
                        'banned_player_id' => $player1->getKey(),
                        'banner_player_id' => $player2->getKey(),
                        'reason' => $ban1->reason,
                        'is_active' => true,
                        'expires_at' => null,
                        'created_at' => $ban1->created_at->timestamp,
                        'updated_at' => $ban1->updated_at->timestamp,
                    ],
                    [
                        'id' => $ban2->getKey(),
                        'server_id' => $server->getKey(),
                        'banned_player_id' => $player1->getKey(),
                        'banner_player_id' => $player2->getKey(),
                        'reason' => $ban2->reason,
                        'is_active' => false,
                        'expires_at' => null,
                        'created_at' => $ban2->created_at->timestamp,
                        'updated_at' => $ban2->updated_at->timestamp,
                    ],
                ],
            ])
            ->assertSuccessful();
    }

    public function test_shows_nothing()
    {
        $this->authoriseTokenFor(ScopeKey::BAN_LOOKUP);

        $player1 = MinecraftPlayer::factory()->create(['uuid' => 'uuid1']);

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: [
                'player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
                'player_id' => $player1->uuid,
            ])
            ->assertJson([
                'data' => [],
            ])
            ->assertSuccessful();
    }
}