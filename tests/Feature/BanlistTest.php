<?php

namespace Tests\Feature;

use App\Entities\Accounts\Models\Account;
use App\Entities\Bans\Models\GameBan;
use App\Entities\Players\Models\MinecraftPlayer;
use App\Entities\Servers\Models\Server;
use App\Entities\Servers\Models\ServerCategory;
use Tests\TestCase;

class BanlistTest extends TestCase
{
    private function makeMinecraftPlayer(): MinecraftPlayer
    {
        return MinecraftPlayer::factory()
            ->for(Account::factory())
            ->create();
    }

    public function testActiveBanIsShownOnList()
    {
        $ban = GameBan::factory()
            ->for(Server::factory()->for(ServerCategory::factory()))
            ->for($this->makeMinecraftPlayer(), 'bannedPlayer')
            ->for($this->makeMinecraftPlayer(), 'staffPlayer')
            ->active()
            ->create();

        $this->get(route('front.banlist'))
            ->assertSee($ban->reason);
    }

    public function testInactiveBanIsNotShownOnList()
    {
        $ban = GameBan::factory()
            ->for(Server::factory()->for(ServerCategory::factory()))
            ->for($this->makeMinecraftPlayer(), 'bannedPlayer')
            ->for($this->makeMinecraftPlayer(), 'staffPlayer')
            ->inactive()
            ->create();

        $this->get(route('front.banlist'))
            ->assertDontSee($ban->reason);
    }
}
