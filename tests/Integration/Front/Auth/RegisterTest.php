<?php

use App\Domains\Activation\Notifications\AccountNeedsActivationNotification;
use App\Models\Account;
use App\Models\Role;
use Illuminate\Support\Facades\Notification;
use Tests\Support\RegistrationTestHelpers;

uses(RegistrationTestHelpers::class);

it('redirects to account settings if logged in', function () {
    $this->actingAs(Account::factory()->create())
        ->get(route('front.register'))
        ->assertRedirect(route('front.account.profile'));
});

it('shows a page', function () {
    $this->get(route('front.register'))
        ->assertSuccessful();
});

describe('validation errors', function () {
    it('throws if fields are missing', function () {
        $this->post(route('front.register.submit'), [])
            ->assertInvalid(['email', 'username', 'password', 'captcha-response', 'terms']);
    });

    it('throws if password is too short', function () {
        $this->mockCaptcha();

        $unactivatedAccount = $this->createUnactivatedAccount();
        $formData = $this->validFormDataFor($unactivatedAccount);
        $formData['password'] = '123';

        $this->post(route('front.register.submit'), $formData)
            ->assertInvalid(['password']);
    });

    it('throws if username is already in use', function () {
        $this->mockCaptcha();

        Account::factory()->create(['username' => 'taken']);
        $this->assertDatabaseHas('accounts', ['username' => 'taken']);

        $unactivatedAccount = $this->createUnactivatedAccount(['username' => 'taken']);
        $formData = $this->validFormDataFor($unactivatedAccount);

        $this->post(route('front.register.submit'), $formData)
            ->assertInvalid(['username']);
    });

    it('throws if email is already in use', function () {
        $this->mockCaptcha();

        Account::factory()->create(['email' => 'me@pcbmc.co']);
        $this->assertDatabaseHas('accounts', ['email' => 'me@pcbmc.co']);

        $unactivatedAccount = $this->createUnactivatedAccount(['email' => 'me@pcbmc.co']);
        $formData = $this->validFormDataFor($unactivatedAccount);

        $this->post(route('front.register.submit'), $formData)
            ->assertInvalid(['email']);
    });

    it('throws if captcha is invalid', function () {
        $this->mockCaptcha(passed: false);

        $unactivatedAccount = $this->createUnactivatedAccount();
        $formData = $this->validFormDataFor($unactivatedAccount);

        $this->post(route('front.register.submit'), $formData)
            ->assertInvalid(['captcha-response']);
    });
});

describe('successful submit', function () {
    beforeEach(function () {
        Role::factory()->create(['is_default' => true]);
        $this->mockCaptcha();
    });

    it('creates an unactivated account from valid form data', function () {
        $unactivatedAccount = $this->createUnactivatedAccount();
        $formData = $this->validFormDataFor($unactivatedAccount);

        $this->post(route('front.register.submit'), $formData)
            ->assertSessionHasNoErrors()
            ->assertValid(['email', 'username', 'password', 'captcha-response', 'terms']);

        $this->assertDatabaseHas('accounts', [
            'email' => $unactivatedAccount->email,
            'username' => $unactivatedAccount->username,
            'activated' => false,
        ]);
    });

    it('hashes the password', function () {
        $unactivatedAccount = $this->createUnactivatedAccount();
        $formData = $this->validFormDataFor($unactivatedAccount);
        $this->post(route('front.register.submit'), $formData);

        $account = Account::where('email', $unactivatedAccount->email)->firstOrFail();

        expect($account->password)
            ->not
            ->toBe($unactivatedAccount->password);
    });

    it('sends an email to activate the account', function () {
        Notification::fake();
        $unactivatedAccount = $this->createUnactivatedAccount();
        $formData = $this->validFormDataFor($unactivatedAccount);

        $this->post(route('front.register.submit'), $formData);

        $account = Account::where('email', $unactivatedAccount->email)->firstOrFail();

        Notification::assertSentTo($account, AccountNeedsActivationNotification::class);
    });

    it('authenticates as user', function () {
        $unactivatedAccount = $this->createUnactivatedAccount();
        $formData = $this->validFormDataFor($unactivatedAccount);

        $this->assertGuest();
        $this->post(route('front.register.submit'), $formData);

        $account = Account::whereEmail($formData['email'])->firstOrFail();
        $this->assertAuthenticatedAs($account);
    });

    it('redirects to activation flow', function () {
        $unactivatedAccount = $this->createUnactivatedAccount();
        $formData = $this->validFormDataFor($unactivatedAccount);

        $this->post(route('front.register.submit'), $formData)
            ->assertRedirect(route('front.activate'));
    });
});
