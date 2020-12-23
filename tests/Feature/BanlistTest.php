<?php

namespace Tests\Feature;

use App\Entities\Bans\Models\GameBan;
use App\Entities\Players\Models\MinecraftPlayer;
use App\Entities\Servers\Models\Server;
use Tests\TestCase;

class BanlistTest extends TestCase
{
    public function testActiveBanIsShownOnList()
    {
        $ban = GameBan::factory()
            ->has(Server::factory()->withNewCategory())
            ->hasBannedPlayer(MinecraftPlayer::factory())
            ->hasStaffPlayer(MinecraftPlayer::factory())
            ->create();

        $this->get(route('front.banlist'))
            ->assertSee($ban->reason);
    }

    public function testInactiveBanIsNotShownOnList()
    {
        $ban = GameBan::factory()
            ->inactive()
            ->has(Server::factory()->withNewCategory())
            ->hasBannedPlayer(MinecraftPlayer::factory())
            ->hasStaffPlayer(MinecraftPlayer::factory())
            ->create();

        $this->get(route('front.banlist'))
            ->assertDontSee($ban->reason);
    }
}
