<?php

use App\Models\Account;
use App\Models\AccountActivation;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Support\Facades\Session;

describe('message page', function () {
    it('redirects away if already activated', function () {
        $account = Account::factory()->create();

        $this->actingAs($account)
            ->get(route('front.activate'))
            ->assertRedirectToRoute('front.account.profile');
    });

    it('shows page if not activated', function () {
        $account = Account::factory()
            ->unactivated()
            ->create();

        $this->actingAs($account)
            ->get(route('front.activate'))
            ->assertSuccessful();
    });
});

describe('account activation', function () {
    beforeEach(function () {
        $this->account = Account::factory()
            ->unactivated()
            ->create();
    });

    it('redirects to login if not logged in', function () {
        $this->assertGuest();
        $this->withoutMiddleware(ValidateSignature::class)
            ->get(route('front.activate.verify', ['token' => 'foobar']))
            ->assertRedirectToRoute('front.login');
    });

    it('throws exception if signed url is invalid', function () {
        $this->actingAs($this->account)
            ->get(route('front.activate.verify', ['token' => 'foobar']))
            ->assertForbidden();
    });

    it('throws exception if no token found', function () {
        $this->withoutMiddleware(ValidateSignature::class)
            ->actingAs($this->account)
            ->get(route('front.activate.verify', ['token' => 'invalid']))
            ->assertNotFound();
    });

    it('redirects to account profile if already activated', function () {
        $account = Account::factory()->create();
        $activation = AccountActivation::factory()
            ->create(['account_id' => $account->getKey()]);

        $this->withoutMiddleware(ValidateSignature::class)
            ->actingAs($account)
            ->get(route('front.activate.verify', ['token' => $activation->token]))
            ->assertRedirectToRoute('front.account.profile');
    });

    it('activates the account', function () {
        expect($this->account->activated)
            ->toBeFalse();

        $activation = AccountActivation::factory()
            ->create(['account_id' => $this->account->getKey()]);

        $this->withoutMiddleware(ValidateSignature::class)
            ->actingAs($this->account)
            ->get(route('front.activate.verify', ['token' => $activation->token]));

        $this->account->refresh();

        expect($this->account->activated)
            ->toBeTrue();
    });

    it('deletes the activation token', function () {
        $activation = AccountActivation::factory()
            ->create(['account_id' => $this->account->getKey()]);

        $this->withoutMiddleware(ValidateSignature::class)
            ->actingAs($this->account)
            ->get(route('front.activate.verify', ['token' => $activation->token]));

        expect(AccountActivation::where('id', $activation->getKey())->first())
            ->toBeNull();
    });

    it('redirects to intent if present', function () {
        $account = Account::factory()
            ->unactivated()
            ->create();

        $activation = AccountActivation::factory()
            ->create(['account_id' => $account->getKey()]);

        Session::put('url.intended', '/my/path');

        $this->withoutMiddleware(ValidateSignature::class)
            ->actingAs($account)
            ->get(route('front.activate.verify', ['token' => $activation->token]))
            ->assertRedirect('/my/path');

        Session::remove('url.intended');
    });
});
