<?php

use App\Http\Middleware\MfaGate;
use App\Models\Account;

beforeEach(function () {
    $this->formEndpoint = route('front.login');
    $this->submitEndpoint = route('front.login.submit');

    $this->account = Account::factory()
        ->passwordHashed('secret')
        ->create();

    $this->validCredentials = [
        'email' => $this->account->email,
        'password' => 'secret',
    ];
});

it('redirects to account settings if logged in', function () {
    $this->actingAs($this->account)
        ->get($this->formEndpoint)
        ->assertRedirectToRoute('front.account.settings');
});

it('redirects to verification flow if account not activated', function () {
    $account = Account::factory()
        ->passwordHashed('secret')
        ->unactivated()
        ->create();

    $response = $this->post($this->submitEndpoint, [
        'email' => $account->email,
        'password' => 'secret',
    ]);
    $response->assertRedirectToRoute('front.activate', ['email' => $account->email]);
});

describe('validation errors', function () {
    it('throws if credentials are wrong', function () {
        $this->validCredentials['password'] = 'wrong';

        $this->post($this->submitEndpoint, $this->validCredentials)
            ->assertInvalid(['error' => 'Email or password is incorrect']);

        $this->assertGuest();
    });

    it('throws if email is empty', function () {
        unset($this->validCredentials['email']);

        $this->post($this->submitEndpoint, $this->validCredentials)
            ->assertInvalid(['email']);
    });

    it('throws if password is empty', function () {
        unset($this->validCredentials['password']);

        $this->post($this->submitEndpoint, $this->validCredentials)
            ->assertInvalid(['password']);
    });
});

describe('successful login', function () {
   it('starts session', function () {
       $this->assertGuest();
       $this->post($this->submitEndpoint, $this->validCredentials);
       $this->assertAuthenticatedAs($this->account);
   });

   it('redirects to original destination', function () {
       $this->get(route('front.account.billing'))
           ->assertRedirect($this->formEndpoint);

       $this->post(route('front.login.submit'), $this->validCredentials)
           ->assertRedirect(route('front.account.billing'));
   });

   it('updates last login timestamp', function () {
       $oldLoginTime = $this->account->last_login_at;
       $oldLoginIp = $this->account->last_login_ip;

       $this->post($this->submitEndpoint, $this->validCredentials)
           ->assertSessionHasNoErrors();

       $this->account->refresh();

       expect($this->account->last_login_ip)
           ->not
           ->toBe($oldLoginIp);

       expect($this->account->last_login_at)
           ->not
           ->toBe($oldLoginTime);
   });
});

describe('mfa', function () {
   it('sets mfa flag on login', function () {
       $account = Account::factory()
           ->passwordHashed('secret')
           ->hasFinishedTotp()
           ->create();

       $this->post($this->submitEndpoint, [
           'email' => $account->email,
           'password' => 'secret',
       ])->assertSessionHas(MfaGate::NEEDS_MFA_KEY);
   });
});
