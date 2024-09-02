<?php

use App\Models\Account;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Support\Facades\Session;

it('shows a page', function () {
    $this->get(route('front.activate'))
        ->assertSuccessful();
});

describe('account activation', function () {
    it('throws exception if signed url is invalid', function () {
        $this->get(route('front.activate.verify'))
            ->assertForbidden();
    });

    it('throws exception if no account found', function () {
        $this->withoutMiddleware(ValidateSignature::class)
            ->get(route('front.activate.verify', ['email' => 'invalid@foo.bar']))
            ->assertNotFound();
    });

    it('throws exception if account already activated', function () {
        $account = Account::factory()->create();

        $this->assertDatabaseHas('accounts', [
            'email' => $account->email,
        ]);

        $this->withoutMiddleware(ValidateSignature::class)
            ->get(route('front.activate.verify', ['email' => $account->email]))
            ->assertStatus(410);
    });

    it('activates the account', function () {
        $account = Account::factory()
            ->unactivated()
            ->create();

        $this->withoutMiddleware(ValidateSignature::class)
            ->get(route('front.activate.verify', ['email' => $account->email]))
            ->assertRedirectToRoute('front.login');

        expect(Account::whereEmail($account->email)->first()->activated)
            ->toBeTrue();
    });

    it('redirects to intent if present', function () {
        $account = Account::factory()
            ->unactivated()
            ->create();

        Session::put('url.intended', '/my/path');

        $this->withoutMiddleware(ValidateSignature::class)
            ->get(route('front.activate.verify', ['email' => $account->email]))
            ->assertRedirect('/my/path');

        Session::remove('url.intended');
    });
});
