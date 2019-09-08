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
        $this->adminAccount = factory(Account::class)->create();
        $adminGroup = Group::create([
            'name' => 'Administrator',
            'is_admin' => true
        ]);

        $this->adminAccount->groups()->attach($adminGroup->group_id);

    }

    public function testAccountShownInList()
    {
        $account = factory(Account::class)->create();
        $this->actingAs($this->adminAccount)
            ->get(route('front.panel.accounts.index'))
            ->assertSee($account->username);
    }

    public function testUnactivatedAccountShownInList()
    {
        $account = factory(Account::class)->state('unactivated')->create();

        $this->actingAs($this->adminAccount)
            ->get(route('front.panel.accounts.index'))
            ->assertSee($account->username);
    }
}
