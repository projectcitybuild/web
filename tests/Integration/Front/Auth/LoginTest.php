<?php

use Admin9\OidcServer\Authentication\AuthenticationRecord;
use Admin9\OidcServer\Contracts\AuthenticationRecorder;
use App\Http\Middleware\MfaAuthenticated;
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
        ->assertRedirectToRoute('front.account.profile');
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

    it('records authentication after a non-mfa login', function () {
        $recorder = Mockery::mock(AuthenticationRecorder::class);
        $recorder->shouldReceive('markAuthenticated')
            ->once()
            ->with('web', Mockery::on(fn (Account $account) => $account->is($this->account)))
            ->andReturn(new AuthenticationRecord(
                'web', Account::class, (string) $this->account->getAuthIdentifier(), now()->timestamp, str_repeat('a', 64),
            ));
        $this->app->instance(AuthenticationRecorder::class, $recorder);

        $this->post($this->submitEndpoint, $this->validCredentials)
            ->assertSessionHasNoErrors();
    });

    it('redirects to original destination', function () {
        $this->get(route('front.account.settings.billing'))
            ->assertRedirect($this->formEndpoint);

        $this->post(route('front.login.submit'), $this->validCredentials)
            ->assertRedirect(route('front.account.settings.billing'));
    });

    it('redirects to account profile if no original destination', function () {
        $this->post(route('front.login.submit'), $this->validCredentials)
            ->assertRedirect(route('front.account.profile'));
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

        $recorder = Mockery::mock(AuthenticationRecorder::class);
        $recorder->shouldNotReceive('markAuthenticated');
        $this->app->instance(AuthenticationRecorder::class, $recorder);

        $this->post($this->submitEndpoint, [
            'email' => $account->email,
            'password' => 'secret',
        ])->assertSessionHas(MfaAuthenticated::NEEDS_MFA_KEY);
    });
});
