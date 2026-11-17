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
