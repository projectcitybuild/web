<?php

namespace Panel;

use App\Domains\Panel\Data\PanelGroupScope;
use App\Models\Account;
use App\Models\Donation;
use App\Models\DonationPerk;
use App\Models\DonationTier;
use App\Models\Group;
use Tests\IntegrationTestCase;

class PanelDonationPerkDeleteTest extends IntegrationTestCase
{
    private Account $adminAccount;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminAccount = $this->adminAccount(scopes: [
            PanelGroupScope::ACCESS_PANEL,
            PanelGroupScope::MANAGE_DONATIONS,
        ]);
    }

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

        $this->actingAs($this->adminAccount)
            ->delete(route('front.panel.donation-perks.destroy', $donationPerk))
            ->assertRedirect(route('front.panel.donations.show', $donation));

        $this->assertDatabaseCount(DonationPerk::tableName(), 0);
    }
}
