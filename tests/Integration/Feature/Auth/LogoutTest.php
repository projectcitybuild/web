<?php

use App\Models\Account;

it('ends session', function () {
    $account = Account::factory()->create();

    $this->actingAs($account);
    $this->assertAuthenticatedAs($account);
    $this->get(route('front.logout'));
    $this->assertGuest();
});

it('redirects to login page', function () {
    $account = Account::factory()->create();

    $this->actingAs($account)
        ->get(route('front.logout'))
        ->assertRedirectToRoute('front.login');
});
