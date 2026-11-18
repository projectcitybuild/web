<?php

namespace Tests\Integration\Front;

use App\Models\Account;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;

it('shows active bans on the list', function () {
    $ban = GamePlayerBan::factory()
        ->bannedPlayer(MinecraftPlayer::factory()->for(Account::factory()))
        ->bannedBy(MinecraftPlayer::factory()->for(Account::factory()))
        ->create();

    $this->get(route('front.banlist'))
        ->assertSee($ban->reason);
});

it('hides inactive bans from the list', function () {
    $ban = GamePlayerBan::factory()
        ->bannedPlayer(MinecraftPlayer::factory()->for(Account::factory()))
        ->bannedBy(MinecraftPlayer::factory()->for(Account::factory()))
        ->inactive()
        ->create();

    $this->get(route('front.banlist'))
        ->assertDontSee($ban->reason);
});
