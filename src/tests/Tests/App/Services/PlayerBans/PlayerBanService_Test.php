<?php
namespace Tests\Services;

use Tests\TestCase;
use App\Services\PlayerBans\PlayerBanService;
use App\Entities\GamePlayerType;
use App\Entities\Bans\Models\GameBan;
use App\Services\PlayerBans\Exceptions\UserAlreadyBannedException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlayerBanService_Test extends TestCase
{
    use RefreshDatabase;

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

        // when...
        $service->ban($serverId,
                      $bannedPlayerId,
                      $bannedPlayerType,
                      $bannedPlayerAlias,
                      $staffPlayerId,
                      $staffPlayerType,
                      $reason);

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
        $service->ban($serverId,
                      $bannedPlayerId,
                      $bannedPlayerType,
                      $bannedPlayerAlias,
                      $staffPlayerId,
                      $staffPlayerType,
                      $reason);
    }
}