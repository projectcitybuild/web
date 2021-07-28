<?php

namespace Tests\Feature;

use App\Entities\Accounts\Models\Account;
use App\Entities\Donations\Models\Donation;
use App\Entities\Donations\Models\DonationPerk;
use Carbon\Carbon;
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

    public function testShowsLifetimeDonation()
    {
        $account = Account::factory()->create();

        DonationPerk::factory()
            ->for($account)
            ->for(Donation::factory()->for($account))
            ->lifetime()
            ->create();

        $this->actingAs($account);

        $this->get(route('front.account.donations'))
            ->assertOk()
            ->assertSee('Lifetime');
    }
}
