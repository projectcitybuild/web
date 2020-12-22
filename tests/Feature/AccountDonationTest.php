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
        $this->actingAs(factory(Account::class)->create());

        $this->get(route('front.account.donations'))
            ->assertOk()
            ->assertSee("You haven't donated yet!", false);
    }

    public function testShowsTemporaryDonation()
    {
        $account = Account::factory()->create();
        $this->actingAs($account);

        $donation = Donation::factory()->create(['account_id' => $account->getKey()]);
        $perkData = DonationPerk::factory()->create(['account_id' => $account->getKey()]);
        $donation->perks()->create($perkData);

        $this->get(route('front.account.donations'))
            ->assertOk()
            ->assertSee(Carbon::instance($perkData["expires_at"])->toFormattedDateString());
    }

    public function testShowsLifetimeDonation()
    {
        $account = Account::factory()->create();
        $this->actingAs($account);

        $donation = Donation::factory()->create(['account_id' => $account->getKey()]);
        DonationPerk::factory()->lifetime()->for($donation)->create();

        $this->get(route('front.account.donations'))
            ->assertOk()
            ->assertSee("Lifetime");
    }
}
