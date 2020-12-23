<?php

namespace Tests\Feature;

use App\Entities\Bans\Models\GameBan;
use App\Entities\Players\Models\MinecraftPlayer;
use App\Entities\Servers\Models\Server;
use App\Entities\Servers\Models\ServerCategory;
use Tests\TestCase;

class BanlistTest extends TestCase
{
    public function testActiveBanIsShownOnList()
    {
        $ban = GameBan::factory()
            ->hasServer(Server::factory()->has(ServerCategory::factory()))
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
            ->hasServer(Server::factory()->has(ServerCategory::factory()))
            ->hasBannedPlayer(MinecraftPlayer::factory())
            ->hasStaffPlayer(MinecraftPlayer::factory())
            ->create();

        $this->get(route('front.banlist'))
            ->assertDontSee($ban->reason);
    }
}
