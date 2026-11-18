<?php

namespace Tests\Integration\Front;

use App\Models\Account;
use App\Models\MinecraftPlayer;

it('shows linked Minecraft accounts in the list', function () {
    $account = Account::factory()->create();
    $mcPlayer = MinecraftPlayer::factory()->for($account)->create();

    $this->actingAs($account);

    $this->get(route('front.account.games'))
        ->assertOk()
        ->assertSee($mcPlayer->uuid);
});

it('shows linked Minecraft accounts without aliases', function () {
    $account = Account::factory()->create();
    $this->actingAs($account);

    $noAliasMcPlayer = MinecraftPlayer::factory()
        ->for($account)
        ->create();

    $this->get(route('front.account.games'))
        ->assertOk()
        ->assertSee($noAliasMcPlayer->uuid);
});

it('allows an account to unlink its own Minecraft account', function () {
    $account = Account::factory()->create();
    $mcPlayer = MinecraftPlayer::factory()->for($account)->create();
    $this->actingAs($account);

    $this->delete(route('front.account.games.delete', $mcPlayer->id))
        ->assertRedirect();

    $this->assertDatabaseHas(MinecraftPlayer::tableName(), [
        'uuid' => $mcPlayer->uuid,
        'account_id' => null,
    ]);
});

it('forbids unlinking another account Minecraft account', function () {
    $account = Account::factory()->create();
    $mcPlayer = MinecraftPlayer::factory()->for($account)->create();
    $this->actingAs(Account::factory()->create());

    $this->delete(route('front.account.games.delete', $mcPlayer->id))
        ->assertForbidden();
});
