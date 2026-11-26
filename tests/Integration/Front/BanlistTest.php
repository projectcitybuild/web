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

it('searches active bans by player name and partial UUID', function () {
    $player = MinecraftPlayer::factory()
        ->for(Account::factory())
        ->create([
            'alias' => 'Player',
            'uuid' => '12345678-1234-1234-1234-123456789abc',
        ]);
    $ban = GamePlayerBan::factory()
        ->bannedPlayer($player)
        ->create(['reason' => 'Searchable ban']);

    $this->get(route('front.banlist', ['query' => 'Player']))
        ->assertOk()
        ->assertSee($ban->reason);

    $this->get(route('front.banlist', ['query' => '12345678']))
        ->assertOk()
        ->assertSee($ban->reason);
});

it('returns paginated ban data for live searches', function () {
    $player = MinecraftPlayer::factory()
        ->for(Account::factory())
        ->create([
            'alias' => 'Player',
            'uuid' => '12345678-1234-1234-1234-123456789abc',
        ]);
    GamePlayerBan::factory()
        ->bannedPlayer($player)
        ->create(['reason' => 'Searchable ban']);

    $this->getJson(route('front.banlist', ['query' => '12345678']))
        ->assertOk()
        ->assertJsonPath('data.0.name', 'Player')
        ->assertJsonPath('data.0.uuid', '12345678-1234-1234-1234-123456789abc')
        ->assertJsonMissingPath('data.0.additional_info');
});
