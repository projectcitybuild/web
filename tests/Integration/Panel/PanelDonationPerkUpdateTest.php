<?php

namespace Panel;

use App\Domains\Panel\Data\PanelGroupScope;
use App\Models\Account;
use App\Models\Donation;
use App\Models\DonationPerk;
use Tests\IntegrationTestCase;

class PanelDonationPerkUpdateTest extends IntegrationTestCase
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

    public function test_valid_update()
    {
        $donationPerk = DonationPerk::factory()->for(Donation::factory())->for(Account::factory())->notExpired()->create();

        $newExpiry = now()->addMonth();

        $this->actingAs($this->adminAccount)
            ->put(route('manage.donation-perks.update', $donationPerk), [
                'donation_id' => $donationPerk->donation->getKey(),
                'account_id' => $donationPerk->account->getKey(),
                'is_active' => true,
                'is_lifetime_perks' => false,
                'expires_at' => $newExpiry,
                'created_at' => $donationPerk->created_at,
            ])->assertSessionHasNoErrors()
            ->assertRedirect(route('manage.donations.show', $donationPerk->donation));

        $this->assertDatabaseHas(DonationPerk::tableName(), [
            'donation_perks_id' => $donationPerk->getKey(),
            'expires_at' => $newExpiry,
        ]);
    }
}
