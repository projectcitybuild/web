<?php

namespace Tests\Integration\Front;

use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Tests\Support\AccountSettingsTestActions;

uses(AccountSettingsTestActions::class);

it('changes the password', function () {
    $account = Account::factory()->passwordHashed()->create();
    $newPassword = 'new_password_1234';

    $this->submitPasswordChange($account, 'abcdef123456', $newPassword, $newPassword)
        ->assertSessionHasNoErrors();

    $this->assertTrue(Auth::attempt([
        'email' => $account->email,
        'password' => $newPassword,
    ]));
});

it('rejects an incorrect current password', function () {
    $account = Account::factory()->passwordHashed()->create();
    $this->submitPasswordChange($account, 'wrong', 'password', 'password')
        ->assertSessionHasErrors();
});

it('rejects a mismatched new password confirmation', function () {
    $account = Account::factory()->passwordHashed()->create();
    $this->submitPasswordChange($account, 'secret', 'password', 'different')
        ->assertSessionHasErrors();
});

it('rejects an empty current password', function () {
    $account = Account::factory()->passwordHashed()->create();
    $this->submitPasswordChange($account, '', 'new', 'new')
        ->assertSessionHasErrors();
});

it('rejects an empty new password', function () {
    $account = Account::factory()->passwordHashed()->create();
    $this->submitPasswordChange($account, 'secret', '', '')
        ->assertSessionHasErrors();
});
