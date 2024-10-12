<?php

namespace Tests\Integration\API;

use App\Core\Data\PlayerIdentifierType;
use App\Domains\Bans\Data\UnbanType;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use App\Models\Server;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\IntegrationTestCase;

class APIBanAllTest extends IntegrationTestCase
{
    use RefreshDatabase;

    private const ENDPOINT = 'api/v2/bans/player/all';

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
        $this->postJson(uri: self::ENDPOINT, data: $this->validData())
            ->assertUnauthorized();

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: $this->validData())
            ->assertSuccessful();
    }

    public function test_shows_bans_and_unbans()
    {
        $player1 = MinecraftPlayer::factory()->create(['uuid' => 'uuid1']);
        $player2 = MinecraftPlayer::factory()->create(['uuid' => 'uuid2']);
        $server = Server::factory()->create();

        $ban1 = GamePlayerBan::factory()
            ->bannedPlayer($player1)
            ->bannedBy($player2)
            ->server($server)
            ->create(['created_at' => now()->subDays(1)]);

        $ban2 = GamePlayerBan::factory()
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
                        'expires_at' => null,
                        'created_at' => $ban1->created_at->timestamp,
                        'updated_at' => $ban1->updated_at->timestamp,
                        'unbanned_at' => null,
                        'unbanner_player_id' => null,
                        'unban_type' => null,
                    ],
                    [
                        'id' => $ban2->getKey(),
                        'server_id' => $server->getKey(),
                        'banned_player_id' => $player1->getKey(),
                        'banner_player_id' => $player2->getKey(),
                        'reason' => $ban2->reason,
                        'expires_at' => null,
                        'created_at' => $ban2->created_at->timestamp,
                        'updated_at' => $ban2->updated_at->timestamp,
                        'unbanned_at' => $ban2->unbanned_at->timestamp,
                        'unbanner_player_id' => null,
                        'unban_type' => UnbanType::MANUAL->value,
                    ],
                ],
            ])
            ->assertSuccessful();
    }

    public function test_shows_nothing()
    {
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
