<?php

namespace Tests\Integration\Feature;

use App\Models\Account;
use App\Models\Donation;
use App\Models\DonationPerk;
use Carbon\Carbon;
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
