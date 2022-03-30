<?php

namespace Tests\Feature;

use App\Entities\Models\Eloquent\Account;
use App\Entities\Models\Eloquent\GameBan;
use App\Entities\Models\Eloquent\MinecraftPlayer;
use App\Entities\Models\Eloquent\Server;
use App\Entities\Models\Eloquent\ServerCategory;
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
