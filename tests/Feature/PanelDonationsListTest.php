<?php

namespace Tests\Feature;

use App\Entities\Accounts\Models\Account;
use App\Entities\Donations\Models\Donation;
use App\Entities\Groups\Models\Group;
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
