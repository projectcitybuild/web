<?php

namespace Tests\Integration\Feature;

use App\Models\Account;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use App\Models\Server;
use App\Models\ServerCategory;
use Tests\TestCase;

class BanlistTest extends TestCase
{
    private function makeMinecraftPlayer(): MinecraftPlayer
    {
        return MinecraftPlayer::factory()
            ->for(Account::factory())
            ->create();
    }

    public function test_active_ban_is_shown_on_list()
    {
        $ban = GamePlayerBan::factory()
            ->for(Server::factory()->for(ServerCategory::factory()))
            ->bannedPlayer($this->makeMinecraftPlayer())
            ->bannedBy($this->makeMinecraftPlayer())
            ->create();

        $this->get(route('front.banlist'))
            ->assertSee($ban->reason);
    }

    public function test_inactive_ban_is_not_shown_on_list()
    {
        $ban = GamePlayerBan::factory()
            ->for(Server::factory()->for(ServerCategory::factory()))
            ->bannedPlayer($this->makeMinecraftPlayer())
            ->bannedBy($this->makeMinecraftPlayer())
            ->inactive()
            ->create();

        $this->get(route('front.banlist'))
            ->assertDontSee($ban->reason);
    }
}
