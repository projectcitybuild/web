<?php

namespace Tests\Feature;

use App\Entities\Accounts\Models\Account;
use App\Entities\Donations\Models\Donation;
use App\Entities\Groups\Models\Group;
use App\Http\Actions\SyncUserToDiscourse;
use Mockery;
use Tests\TestCase;

class PanelDonationsListTest extends TestCase
{
    private $adminAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminAccount = Account::factory()->create();

        $adminGroup = Group::create([
            'name' => 'Administrator',
            'can_access_panel' => true
        ]);

        $this->adminAccount->groups()->attach($adminGroup->group_id);
    }

    public function testDonationShownInList()
    {
        $donation = Donation::factory()->create();

        $this->actingAs($this->adminAccount)
            ->get(route('front.panel.donations.index'))
            ->assertSee($donation->donation_id);
    }
}
