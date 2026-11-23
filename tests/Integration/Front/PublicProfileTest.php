<?php

use App\Models\Account;
use App\Models\Badge;
use App\Models\MinecraftPlayer;
use App\Models\MinecraftPlayerSession;

test('public player profile shows session days, visible badges, stats, and skin preview', function () {
    $this->travelTo('2026-05-10 12:00:00');

    $account = Account::factory()->create();
    $visibleBadge = Badge::factory()->create([
        'display_name' => 'Community Builder',
        'unicode_icon' => '★',
        'list_hidden' => false,
    ]);
    $hiddenBadge = Badge::factory()->create([
        'display_name' => 'Secret Achievement',
        'list_hidden' => true,
    ]);
    $account->badges()->attach([$visibleBadge->id, $hiddenBadge->id]);

    $player = MinecraftPlayer::factory()->for($account)->create([
        'alias' => 'ExamplePlayer',
        'nickname' => 'The Builder',
        'sessions' => 42,
        'play_time' => 360_000,
        'afk_time' => 7_200,
        'blocks_placed' => 1_234,
        'blocks_destroyed' => 567,
        'blocks_travelled' => 89_000,
    ]);

    MinecraftPlayerSession::query()->create([
        'player_id' => $player->id,
        'seconds' => 600,
        'starts_at' => '2026-05-09 08:00:00',
        'ends_at' => '2026-05-09 08:10:00',
    ]);
    MinecraftPlayerSession::query()->create([
        'player_id' => $player->id,
        'seconds' => 600,
        'starts_at' => '2026-05-09 17:00:00',
        'ends_at' => '2026-05-09 17:10:00',
    ]);

    $response = $this->get(route('front.profile.show', $player->uuid));

    $response->assertOk()
        ->assertSee('ExamplePlayer')
        ->assertSee('The Builder')
        ->assertSee('Community Builder')
        ->assertDontSee('Secret Achievement')
        ->assertSee('https://minotar.net/body/'.$player->uuid.'/100.png')
        ->assertSee('aria-label="1 day with recorded sessions in the past year"', false)
        ->assertSee('1,234')
        ->assertDontSee($account->email);

    expect(substr_count($response->getContent(), 'data-session-active'))->toBe(1);
});

test('public player profile is available without a linked account', function () {
    $player = MinecraftPlayer::factory()->create([
        'account_id' => null,
        'alias' => 'UnlinkedPlayer',
    ]);

    $this->get(route('front.profile.show', $player->uuid))
        ->assertOk()
        ->assertSee('UnlinkedPlayer')
        ->assertSee('No public badges yet.');
});

test('public player profile returns not found for an unknown UUID', function () {
    $this->get(route('front.profile.show', '00000000000000000000000000000000'))
        ->assertNotFound();
});
