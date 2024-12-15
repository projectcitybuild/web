<?php

namespace Tests\Integration\Panel;

use App\Models\Account;
use App\Models\Donation;
use Tests\TestCase;

class ManageDonationsListTest extends TestCase
{
    public function test_donation_shown_in_list()
    {
        $donation = Donation::factory()->create();

        $this->actingAs($this->adminAccount())
            ->get(route('manage.donations.index'))
            ->assertSee($donation->donation_id);
    }

    public function test_forbidden_unless_admin()
    {
        $this->actingAs($this->adminAccount())
            ->get(route('manage.donations.index'))
            ->assertSuccessful();

        $this->actingAs($this->staffAccount())
            ->get(route('manage.donations.index'))
            ->assertForbidden();

        $this->actingAs(Account::factory()->create())
            ->get(route('manage.donations.index'))
            ->assertForbidden();
    }
}
