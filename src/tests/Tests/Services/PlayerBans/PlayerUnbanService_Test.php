<?php
namespace Tests\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Domains\Services\PlayerBans\Exceptions\UserAlreadyBannedException;
use Domains\Services\PlayerBans\PlayerUnbanService;
use Entities\Bans\Models\GameBan;
use Entities\GamePlayerType;
use Domains\Services\PlayerBans\Exceptions\UserNotBannedException;

class PlayerUnbanService_Test extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    public function testCreatesUnban()
    {
        // given...
        $service = resolve(PlayerUnbanService::class);
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
        $service = resolve(PlayerUnbanService::class);
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
        $service = resolve(PlayerUnbanService::class);

        // expect...
        $this->expectException(UserNotBannedException::class);

        // when...
        $service->unban(1, GamePlayerType::Minecraft(), 2, GamePlayerType::Minecraft());
    }
}