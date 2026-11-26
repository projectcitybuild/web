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

it('sorts active bans by player name in the requested direction', function () {
    $zulu = MinecraftPlayer::factory()
        ->for(Account::factory())
        ->create(['alias' => 'Zulu']);
    $alpha = MinecraftPlayer::factory()
        ->for(Account::factory())
        ->create(['alias' => 'Alpha']);
    GamePlayerBan::factory()->bannedPlayer($zulu)->create();
    GamePlayerBan::factory()->bannedPlayer($alpha)->create();

    $this->getJson(route('front.banlist', ['sort' => 'name', 'direction' => 'asc']))
        ->assertOk()
        ->assertJsonPath('data.0.name', 'Alpha')
        ->assertJsonPath('data.1.name', 'Zulu');

    $this->getJson(route('front.banlist', ['sort' => 'name', 'direction' => 'desc']))
        ->assertOk()
        ->assertJsonPath('data.0.name', 'Zulu')
        ->assertJsonPath('data.1.name', 'Alpha');
});

it('keeps the requested sort when filtering active bans', function () {
    $zulu = MinecraftPlayer::factory()
        ->for(Account::factory())
        ->create(['alias' => 'Filtered Zulu']);
    $alpha = MinecraftPlayer::factory()
        ->for(Account::factory())
        ->create(['alias' => 'Filtered Alpha']);
    $unmatched = MinecraftPlayer::factory()
        ->for(Account::factory())
        ->create(['alias' => 'Unmatched']);
    GamePlayerBan::factory()->bannedPlayer($zulu)->create();
    GamePlayerBan::factory()->bannedPlayer($alpha)->create();
    GamePlayerBan::factory()->bannedPlayer($unmatched)->create();

    $this->getJson(route('front.banlist', [
        'query' => 'Filtered',
        'sort' => 'name',
        'direction' => 'asc',
    ]))
        ->assertOk()
        ->assertJsonPath('total', 2)
        ->assertJsonPath('data.0.name', 'Filtered Alpha')
        ->assertJsonPath('data.1.name', 'Filtered Zulu');
});

it('sorts active bans by reason and ban date on the server', function () {
    $player = MinecraftPlayer::factory()
        ->for(Account::factory())
        ->create();
    GamePlayerBan::factory()
        ->bannedPlayer($player)
        ->create(['reason' => 'Zebra', 'created_at' => now()]);
    GamePlayerBan::factory()
        ->bannedPlayer($player)
        ->create(['reason' => 'Apple', 'created_at' => now()->subDay()]);

    $this->getJson(route('front.banlist', ['sort' => 'reason', 'direction' => 'asc']))
        ->assertOk()
        ->assertJsonPath('data.0.reason', 'Apple')
        ->assertJsonPath('data.1.reason', 'Zebra');

    $this->getJson(route('front.banlist', ['sort' => 'created_at', 'direction' => 'asc']))
        ->assertOk()
        ->assertJsonPath('data.0.reason', 'Apple')
        ->assertJsonPath('data.1.reason', 'Zebra');
});

it('rejects unsupported ban sort columns', function () {
    $this->getJson(route('front.banlist', ['sort' => 'banned_player_id', 'direction' => 'asc']))
        ->assertUnprocessable()
        ->assertJsonValidationErrorFor('sort');
});
