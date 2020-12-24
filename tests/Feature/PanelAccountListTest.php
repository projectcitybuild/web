<?php

namespace Tests\Feature;

use App\Entities\Accounts\Models\Account;
use App\Entities\Groups\Models\Group;
use App\Http\Actions\SyncUserToDiscourse;
use Mockery;
use Tests\TestCase;

class PanelAccountListTest extends TestCase
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

    public function testAccountShownInList()
    {
        $account = Account::factory()->create();

        $this->actingAs($this->adminAccount)
            ->get(route('front.panel.accounts.index'))
            ->assertSee($account->username);
    }

    public function testUnactivatedAccountShownInList()
    {
        $account = Account::factory()->unactivated()->create();

        $this->actingAs($this->adminAccount)
            ->get(route('front.panel.accounts.index'))
            ->assertSee($account->username);
    }
}
