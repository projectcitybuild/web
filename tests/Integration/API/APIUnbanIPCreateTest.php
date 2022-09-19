<?php

namespace Tests\Integration\API;

use Domain\Bans\UnbanType;
use Domain\ServerTokens\ScopeKey;
use Entities\Models\Eloquent\GameIPBan;
use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Models\PlayerIdentifierType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\IntegrationTestCase;

class APIUnbanIPCreateTest extends IntegrationTestCase
{
    use RefreshDatabase;

    private const ENDPOINT = 'api/v2/bans/ip/unban';

    protected function setUp(): void
    {
        parent::setUp();

        $this->createServerToken();
    }

    private function validData(): array
    {
        return [
            'ip_address' => '192.168.0.1',
            'unbanner_player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
            'unbanner_player_id' => 'uuid',
        ];
    }

    public function test_requires_scope()
    {
        GameIPBan::factory()
            ->bannedBy(MinecraftPlayer::factory())
            ->create(['ip_address' => '192.168.0.1']);

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

        $player = MinecraftPlayer::factory()->create(['uuid' => 'uuid1']);

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: [
                'ip_address' => '192.168.0.1',
                'unbanner_player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
                'unbanner_player_id' => $player->uuid,
                'reason' => 'reason',
            ])
            ->assertJson([
                'error' => [
                    'id' => 'ip_not_banned',
                    'title' => '',
                    'detail' => 'This IP address is not currently banned',
                    'status' => 400,
                ],
            ]);
    }

    public function test_unbans_ban()
    {
        $this->authoriseTokenFor(ScopeKey::BAN_UPDATE);

        $player = MinecraftPlayer::factory()->create();

        $ban = GameIPBan::factory()
            ->bannedBy($player)
            ->create();

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: [
                'ip_address' => $ban->ip_address,
                'unbanner_player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
                'unbanner_player_id' => $player->uuid,
            ])
            ->assertSuccessful();

        $this->assertDatabaseHas(
            table: GameIPBan::getTableName(),
            data: [
                'id' => $ban->getKey(),
                'unbanned_at' => now(),
                'unbanner_player_id' => $player->getKey(),
                'unban_type' => UnbanType::MANUAL->value,
            ],
        );
    }
}