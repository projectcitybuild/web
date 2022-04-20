<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\Donation;
use Entities\Models\Eloquent\DonationPerk;
use Tests\TestCase;

class AccountDonationTest extends TestCase
{
    public function testShowsNoDonations()
    {
        $this->actingAs(Account::factory()->create());

        $this->get(route('front.account.donations'))
            ->assertOk()
            ->assertSee('You have not made any donations.');
    }

    public function testShowsTemporaryDonation()
    {
        $expiryDate = Carbon::now()->addDay();

        $account = Account::factory()->create();

        DonationPerk::factory()
            ->for($account)
            ->for(Donation::factory()->for($account))
            ->state(['expires_at' => $expiryDate])
            ->create();

        $this->actingAs($account);

        $this->get(route('front.account.donations'))
            ->assertOk()
            ->assertSee($expiryDate->toFormattedDateString());
    }
}
