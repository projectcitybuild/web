<?php

namespace Tests\Feature;

use Entities\Models\Eloquent\Donation;
use Tests\TestCase;

class PanelDonationsListTest extends TestCase
{
    public function testDonationShownInList()
    {
        $donation = Donation::factory()->create();

        $this->actingAs($this->adminAccount())
            ->get(route('front.panel.donations.index'))
            ->assertSee($donation->donation_id);
    }
}
