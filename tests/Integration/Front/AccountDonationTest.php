<?php

namespace Tests\Integration\Front;

use App\Models\Account;

it('shows an empty state when the account has no donations', function () {
    $this->actingAs(Account::factory()->create());

    $this->get(route('front.account.donations'))
        ->assertOk()
        ->assertSee('You have not made any donations');
});
