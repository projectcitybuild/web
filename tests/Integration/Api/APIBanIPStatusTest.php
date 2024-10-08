<?php

namespace Tests\Integration\API;

use App\Models\GameIPBan;
use App\Models\MinecraftPlayer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\IntegrationTestCase;

class APIBanIPStatusTest extends IntegrationTestCase
{
    use RefreshDatabase;

    private const ENDPOINT = 'api/v2/bans/ip/status';

    protected function setUp(): void
    {
        parent::setUp();

        $this->createServerToken();
    }

    private function validData(): array
    {
        return [
            'ip_address' => '192.168.0.1',
        ];
    }

    public function test_requires_scope()
    {
        $this->withAuthorizationServerToken()
            ->getJson(uri: self::ENDPOINT.'?'.http_build_query($this->validData()))
            ->assertForbidden();

        $this->withAuthorizationServerToken()
            ->getJson(uri: self::ENDPOINT.'?'.http_build_query($this->validData()))
            ->assertSuccessful();
    }

    public function test_returns_ban()
    {
        $player = MinecraftPlayer::factory()->create();

        $ban = GameIPBan::factory()
            ->bannedBy($player)
            ->create();

        $this->withAuthorizationServerToken()
            ->getJson(uri: self::ENDPOINT.'?'.http_build_query([
                'ip_address' => $ban->ip_address,
            ]))
            ->assertJson([
                'data' => [
                    'id' => $ban->getKey(),
                    'ip_address' => $ban->ip_address,
                    'reason' => $ban->reason,
                    'banner_player_id' => $player->getKey(),
                    'created_at' => $ban->created_at->timestamp,
                    'updated_at' => $ban->updated_at->timestamp,
                    'unbanned_at' => null,
                    'unbanner_player_id' => null,
                    'unban_type' => null,
                ],
            ])
            ->assertSuccessful();
    }

    public function test_not_banned_when_no_ban()
    {
        $this->withAuthorizationServerToken()
            ->getJson(uri: self::ENDPOINT.'?'.http_build_query([
                'ip_address' => '192.168.0.1',
            ]))
            ->assertJson([
                'data' => [],
            ])
            ->assertSuccessful();
    }

    public function test_not_banned_when_unbanned()
    {
        $player = MinecraftPlayer::factory()->create();

        $ban = GameIPBan::factory()
            ->bannedBy($player)
            ->inactive()
            ->create();

        $this->withAuthorizationServerToken()
            ->getJson(uri: self::ENDPOINT.'?'.http_build_query([
                'ip_address' => $ban->ip_address,
            ]))
            ->assertJson([
                'data' => [],
            ])
            ->assertSuccessful();
    }
}
