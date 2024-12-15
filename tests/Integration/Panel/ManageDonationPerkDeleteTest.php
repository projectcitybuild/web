<?php

namespace Tests\Integration\Panel;

use App\Models\Account;
use App\Models\Donation;
use App\Models\DonationPerk;
use App\Models\DonationTier;
use App\Models\Group;
use Tests\TestCase;

class ManageDonationPerkDeleteTest extends TestCase
{
    public function test_delete_donation_perk()
    {
        $donor = Account::factory()->create();

        $donation = Donation::factory()
            ->for($donor)
            ->create();

        $group = Group::factory()->create();

        $donationPerk = DonationPerk::factory()
            ->for($donation)
            ->for(DonationTier::factory()->for($group))
            ->for($donor)
            ->create();

        $this->actingAs($this->adminAccount())
            ->delete(route('manage.donation-perks.destroy', $donationPerk))
            ->assertRedirect(route('manage.donations.show', $donation));

        $this->assertDatabaseCount(DonationPerk::tableName(), 0);
    }
}
