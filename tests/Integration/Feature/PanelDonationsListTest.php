<?php

namespace Tests\Integration\Feature;

use App\Domains\Panel\Data\PanelGroupScope;
use App\Models\Donation;
use Tests\IntegrationTestCase;

class PanelDonationsListTest extends IntegrationTestCase
{
    public function test_donation_shown_in_list()
    {
        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::ACCESS_PANEL,
            PanelGroupScope::MANAGE_DONATIONS,
        ]);

        $donation = Donation::factory()->create();

        $this->actingAs($admin)
            ->get(route('front.panel.donations.index'))
            ->assertSee($donation->donation_id);
    }

    public function test_unauthorised_without_scope()
    {
        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::ACCESS_PANEL,
        ]);

        $this->actingAs($admin)
            ->get(route('front.panel.donations.index'))
            ->assertUnauthorized();
    }

    public function test_unauthorised_without_panel_access()
    {
        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::MANAGE_DONATIONS,
        ]);

        $this->actingAs($admin)
            ->get(route('front.panel.donations.index'))
            ->assertUnauthorized();
    }
}
