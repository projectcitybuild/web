<?php

namespace Tests\Integration\Front;

use App\Models\Account;
use Tests\TestCase;

class AccountDonationTest extends TestCase
{
    public function test_shows_no_donations()
    {
        $this->actingAs(Account::factory()->create());

        $this->get(route('front.account.donations'))
            ->assertOk()
            ->assertSee('You have not made any donations');
    }
}
