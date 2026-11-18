<?php

namespace Tests\Integration\Front;

use App\Models\Account;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Support\AccountSettingsTestActions;

uses(WithFaker::class, AccountSettingsTestActions::class);

it('changes the username', function () {
    $account = Account::factory()->create();
    $this->withoutExceptionHandling();

    $newUsername = $this->faker->userName;
    $this->submitUsernameChange($account, $newUsername)
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('accounts', [
        'email' => $account->email,
        'username' => $newUsername,
    ]);
});

it('rejects a username already used by another account', function () {
    $account = Account::factory()->create();
    $otherAccount = Account::factory()->create();

    $this->submitUsernameChange($account, $otherAccount->username)
        ->assertSessionHasErrors();
});

it('rejects an empty username', function () {
    $account = Account::factory()->create();
    $this->submitUsernameChange($account, '')
        ->assertSessionHasErrors();
});

it('rejects the account current username', function () {
    $account = Account::factory()->create();
    $this->submitUsernameChange($account, $account->username)
        ->assertSessionHasErrors();
});
