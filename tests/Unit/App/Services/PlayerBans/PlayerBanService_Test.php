<?php

namespace Tests\Services;

use App\Entities\Bans\Models\GameBan;
use App\Entities\GamePlayerType;
use App\Entities\ServerKeys\Models\ServerKey;
use App\Services\PlayerBans\Exceptions\UserAlreadyBannedException;
use App\Services\PlayerBans\Exceptions\UserNotBannedException;
use App\Services\PlayerBans\PlayerBanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PlayerBanService_Test extends TestCase
{
    use RefreshDatabase;

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
        // given...
        $service = resolve(PlayerBanService::class);

        $serverId = 1;
        $bannedPlayerId = 1;
        $bannedPlayerType = GamePlayerType::Minecraft();
        $bannedPlayerAlias = 'test_player';
        $staffPlayerId = 2;
        $staffPlayerType = GamePlayerType::Minecraft();
        $reason = 'test_reason';
        $serverKey = $this->makeServerKey($serverId);

        // when...
        $service->ban(
            $serverKey,
            $serverId,
            $bannedPlayerId,
            $bannedPlayerType,
            $bannedPlayerAlias,
            $staffPlayerId,
            $staffPlayerType,
            $reason
        );

        // expect...
        $this->assertDatabaseHas('game_network_bans', [
            'server_id' => $serverId,
            'banned_player_id' => $bannedPlayerId,
            'banned_player_type' => $bannedPlayerType->valueOf(),
            'banned_alias_at_time' => $bannedPlayerAlias,
            'staff_player_id' => $staffPlayerId,
            'staff_player_type' => $staffPlayerType->valueOf(),
            'reason' => $reason,
        ]);
    }

    public function testCannotCreateBanWhenAlreadyExists()
    {
        // given...
        $service = resolve(PlayerBanService::class);

        $serverId = 1;
        $bannedPlayerId = 1;
        $bannedPlayerType = GamePlayerType::Minecraft();
        $bannedPlayerAlias = 'test_player';
        $staffPlayerId = 2;
        $staffPlayerType = GamePlayerType::Minecraft();
        $reason = 'test_reason';
        $serverKey = $this->makeServerKey($serverId);

        GameBan::create([
            'server_id' => $serverId,
            'banned_player_id' => $bannedPlayerId,
            'banned_player_type' => $bannedPlayerType->valueOf(),
            'banned_alias_at_time' => $bannedPlayerAlias,
            'staff_player_id' => $staffPlayerId,
            'staff_player_type' => $staffPlayerType->valueOf(),
            'reason' => $reason,
            'is_active' => true,
            'is_global_ban' => true,
        ]);

        // expect...
        $this->expectException(UserAlreadyBannedException::class);

        // when...
        $service->ban(
            $serverKey,
            $serverId,
            $bannedPlayerId,
            $bannedPlayerType,
            $bannedPlayerAlias,
            $staffPlayerId,
            $staffPlayerType,
            $reason
        );
    }

    public function testCreatesUnban()
    {
        // given...
        $service = resolve(PlayerBanService::class);
        $ban = GameBan::create([
            'server_id' => 1,
            'banned_player_id' => 1,
            'banned_player_type' => GamePlayerType::Minecraft,
            'banned_alias_at_time' => 'test_player',
            'staff_player_id' => 2,
            'staff_player_type' => GamePlayerType::Minecraft,
            'reason' => 'test_reason',
            'is_active' => true,
            'is_global_ban' => true,
        ]);

        // when...
        $service->unban(1, GamePlayerType::Minecraft(), 2, GamePlayerType::Minecraft());

        // expect...
        $this->assertDatabaseHas('game_network_unbans', [
            'game_ban_id' => $ban->getKey(),
            'staff_player_id' => 2,
            'staff_player_type' => GamePlayerType::Minecraft,
        ]);
    }

    public function testDeactivatesBan()
    {
        // given...
        $service = resolve(PlayerBanService::class);
        $existingBan = GameBan::create([
            'server_id' => 1,
            'banned_player_id' => 1,
            'banned_player_type' => GamePlayerType::Minecraft,
            'banned_alias_at_time' => 'test_player',
            'staff_player_id' => 2,
            'staff_player_type' => GamePlayerType::Minecraft,
            'reason' => 'test_reason',
            'is_active' => true,
            'is_global_ban' => true,
        ]);

        // when...
        $service->unban(1, GamePlayerType::Minecraft(), 2, GamePlayerType::Minecraft());

        // expect...
        $ban = GameBan::find($existingBan->getKey());
        $this->assertEquals(0, $ban->is_active);
    }

    public function testCannotUnbanIfNotBanned()
    {
        // given...
        $service = resolve(PlayerBanService::class);

        // expect...
        $this->expectException(UserNotBannedException::class);

        // when...
        $service->unban(1, GamePlayerType::Minecraft(), 2, GamePlayerType::Minecraft());
    }
}
