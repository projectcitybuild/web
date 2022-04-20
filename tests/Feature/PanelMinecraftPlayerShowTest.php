<?php

namespace Tests\Feature;

use Entities\Models\Eloquent\GameBan;
use Entities\Models\Eloquent\MinecraftPlayer;
use Tests\TestCase;

class PanelMinecraftPlayerShowTest extends TestCase
{
    public function testBanShown()
    {
        $banningStaff = MinecraftPlayer::factory()->hasAliases(1)->create();
        $bannedPlayer = MinecraftPlayer::factory()
            ->hasAliases(1)
            ->has(GameBan::factory()->bannedBy($banningStaff))
            ->create();

        $this->actingAs($this->adminAccount())
            ->get(route('front.panel.minecraft-players.show', $bannedPlayer))
            ->assertOk()
            ->assertSee(GameBan::first()->reason);
    }

    public function testBanWithNullStaffShown()
    {
        $bannedPlayer = MinecraftPlayer::factory()
            ->hasAliases(1)
            ->has(GameBan::factory()->bannedByConsole())
            ->create();

        $this->actingAs($this->adminAccount())
            ->get(route('front.panel.minecraft-players.show', $bannedPlayer))
            ->assertOk()
            ->assertSee(GameBan::first()->reason);
    }
}
