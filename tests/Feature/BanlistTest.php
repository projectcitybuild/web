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
            ->for(Server::factory()->for(ServerCategory::factory()))
            ->for(MinecraftPlayer::factory(), 'staffPlayer')
            ->for(MinecraftPlayer::factory(), 'bannedPlayer')
            ->create();

        $this->get(route('front.banlist'))
            ->assertSee($ban->reason);
    }

    public function testInactiveBanIsNotShownOnList()
    {
        $ban = GameBan::factory()
            ->inactive()
            ->for(Server::factory()->for(ServerCategory::factory()))
            ->for(MinecraftPlayer::factory(), 'staffPlayer')
            ->for(MinecraftPlayer::factory(), 'bannedPlayer')
            ->create();

        $this->get(route('front.banlist'))
            ->assertDontSee($ban->reason);
    }
}
