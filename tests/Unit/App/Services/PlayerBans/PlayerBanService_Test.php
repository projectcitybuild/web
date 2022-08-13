<?php

namespace Tests\Unit\App\Services\PlayerBans;

use App\Services\PlayerBans\Exceptions\UserNotBannedException;
use App\Services\PlayerBans\PlayerBanService;
use Entities\Models\Eloquent\GameBan;
use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Models\Eloquent\MinecraftPlayerAlias;
use Entities\Models\Eloquent\Server;
use Entities\Models\Eloquent\ServerKey;
use Entities\Models\GamePlayerType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PlayerBanService_Test extends TestCase
{
    use RefreshDatabase;

    private PlayerBanService $service;
    private Server $server;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = resolve(PlayerBanService::class);
        $this->server = Server::factory()->hasCategory()->create();
    }

    private function makeServerKey(int $serverId): ServerKey
    {
        return ServerKey::create([
            'server_id' => $serverId,
            'token' => Str::random(32),
            'can_local_ban' => true,
            'can_global_ban' => true,
            'can_warn' => true,
        ]);
    }

    public function testCreatesBan()
    {
        $bannedPlayerAlias = 'test_player';
        $bannedPlayer = MinecraftPlayer::factory()->create();
        $staffPlayer = MinecraftPlayer::factory()->create();
        $reason = 'test_reason';
        $serverKey = $this->makeServerKey($this->server->getKey());

        $this->service->ban(
            $serverKey,
            $this->server->getKey(),
            $bannedPlayer->uuid,
            $bannedPlayerAlias,
            $staffPlayer->uuid,
            $reason
        );

        $this->assertDatabaseHas('game_network_bans', [
            'server_id' => $this->server->getKey(),
            'banned_player_id' => $bannedPlayer->getKey(),
            'banned_alias_at_time' => $bannedPlayerAlias,
            'staff_player_id' => $staffPlayer->getKey(),
            'reason' => $reason,
        ]);
    }

//    public function testCannotCreateBanWhenAlreadyExists()
//    {
//        $bannedPlayer = MinecraftPlayer::factory()->create();
//        $staffPlayer = MinecraftPlayer::factory()->create();
//        $bannedPlayerType = GamePlayerType::Minecraft();
//        $bannedPlayerAlias = 'test_player';
//        $staffPlayerType = GamePlayerType::Minecraft();
//        $reason = 'test_reason';
//        $serverKey = $this->makeServerKey($this->server->getKey());
//
//        GameBan::create([
//            'server_id' => $this->server->getKey(),
//            'banned_player_id' => $bannedPlayer->getKey(),
//            'banned_player_type' => $bannedPlayerType->valueOf(),
//            'banned_alias_at_time' => $bannedPlayerAlias,
//            'staff_player_id' => $staffPlayer->getKey(),
//            'staff_player_type' => $staffPlayerType->valueOf(),
//            'reason' => $reason,
//            'is_active' => true,
//            'is_global_ban' => true,
//        ]);
//
//        $this->expectException(UserAlreadyBannedException::class);
//
//        $this->service->ban(
//            $serverKey,
//            $this->server->getKey(),
//            $bannedPlayer->getKey(),
//            $bannedPlayerType,
//            $bannedPlayerAlias,
//            $staffPlayer->getKey(),
//            $staffPlayerType,
//            $reason
//        );
//    }

    public function testCreatesUnban()
    {
        $bannedPlayer = MinecraftPlayer::factory()->create();
        $staffPlayer = MinecraftPlayer::factory()->create();
        $ban = GameBan::create([
            'server_id' => $this->server->getKey(),
            'banned_player_id' => $bannedPlayer->getKey(),
            'banned_alias_at_time' => 'test_player',
            'staff_player_id' => $staffPlayer->getKey(),
            'reason' => 'test_reason',
            'is_active' => true,
            'is_global_ban' => true,
        ]);

        $this->service->unban($bannedPlayer->uuid, $staffPlayer->uuid);

        $this->assertDatabaseHas('game_network_unbans', [
            'game_ban_id' => $ban->getKey(),
            'staff_player_id' => $staffPlayer->getKey(),
        ]);
    }

    public function testDeactivatesBan()
    {
        $bannedPlayer = MinecraftPlayer::factory()->create();
        $staffPlayer = MinecraftPlayer::factory()->create();
        $existingBan = GameBan::create([
            'server_id' => $this->server->getKey(),
            'banned_player_id' => $bannedPlayer->getKey(),
            'banned_alias_at_time' => 'test_player',
            'staff_player_id' => $staffPlayer->getKey(),
            'reason' => 'test_reason',
            'is_active' => true,
            'is_global_ban' => true,
            'expires_at' => null
        ]);

        $this->service->unban($bannedPlayer->uuid, $staffPlayer->uuid);

        $ban = GameBan::find($existingBan->getKey());
        $this->assertEquals(0, $ban->is_active);

        $this->assertDatabaseHas('game_network_bans', [
            'game_ban_id' => $ban->getKey(),
            'is_active' => false,
        ]);
    }

    public function testCannotUnbanIfNotBanned()
    {
        $this->expectException(UserNotBannedException::class);

        $this->service->unban(1, 2);
    }
}
