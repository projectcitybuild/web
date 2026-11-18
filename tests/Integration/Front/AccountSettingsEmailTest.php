<?php

namespace Tests\Integration\Front;

use App\Domains\EmailChange\Notifications\VerifyNewEmailAddressNotification;
use App\Models\Account;
use App\Models\EmailChange;
use Illuminate\Support\Facades\Notification;
use Tests\Support\AccountSettingsTestActions;

uses(AccountSettingsTestActions::class);

beforeEach(function () {
    Notification::fake();
});

it('changes the email address', function () {
    $account = Account::factory()->create();
    Notification::assertNothingSent();

    $newEmail = 'valid@email.com';

    $this->submitEmailChange($account, $newEmail);

    $this->assertDatabaseHas('account_email_changes', [
        'email' => $newEmail,
        'account_id' => $account->id,
    ]);

    Notification::assertSentTo(
        Notification::route('mail', $newEmail),
        VerifyNewEmailAddressNotification::class,
    );
});

it('replaces an existing email change request', function () {
    $account = Account::factory()->create();
    EmailChange::create([
        'token' => 'test',
        'account_id' => $account->id,
        'email' => 'foo@bar.com',
        'expires_at' => now()->addDay(),
    ]);
    $this->assertDatabaseHas('account_email_changes', [
        'token' => 'test',
        'account_id' => $account->id,
        'email' => 'foo@bar.com',
    ]);

    $newEmail = 'valid@email.com';

    $this->submitEmailChange($account, $newEmail);

    $this->assertDatabaseMissing('account_email_changes', [
        'token' => 'test',
        'account_id' => $account->id,
        'email' => 'foo@bar.com',
    ]);
});

it('rejects an email address already used by another account', function () {
    $account = Account::factory()->create();
    $otherAccount = Account::factory()->create();

    $this->submitEmailChange($account, $otherAccount->email)
        ->assertSessionHasErrors();
});

it('rejects an empty email address', function () {
    $account = Account::factory()->create();
    $this->submitEmailChange($account, '')
        ->assertSessionHasErrors();
});

it('rejects an invalid email address', function () {
    $account = Account::factory()->create();
    $this->submitEmailChange($account, 'test')
        ->assertSessionHasErrors();
});

it('rejects the account current email address', function () {
    $account = Account::factory()->create();
    $this->submitEmailChange($account, $account->email)
        ->assertSessionHasErrors();
});
