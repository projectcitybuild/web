<?php

use App\Models\Account;
use App\Models\MinecraftPlayer;

test('signed-in homepage avatar uses compact dimensions', function () {
    $account = Account::factory()->create();
    $player = MinecraftPlayer::factory()->for($account)->create();

    $this->actingAs($account)
        ->get(route('front.home'))
        ->assertOk()
        ->assertSee('width="32" height="32"', false)
        ->assertSee('https://minotar.net/helm/'.$player->uuid, false);
});

test('homepage shows truncated version', function () {
    config([
        'app.version' => 'v123456789',
        'app.commit' => 'abcdef0123456789abcdef0123456789abcdef01',
    ]);

    $this->get(route('front.home'))
        ->assertOk()
        ->assertSee('v123456', false)
        ->assertSee('abcdef0', false)
        ->assertDontSee('v123456789', false);
});

test('homepage does not show unknown version', function () {
    $this->get(route('front.home'))
        ->assertOk()
        ->assertDontSee('unknown', false);
});
