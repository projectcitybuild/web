<?php

namespace Tests\Feature;

use App\Entities\Accounts\Models\Account;
use App\Entities\Groups\Models\Group;
use Tests\TestCase;

class PanelAccountListTest extends TestCase
{
    public function testAccountShownInList()
    {
        $account = Account::factory()->create();

        $this->actingAs($this->adminAccount())
            ->get(route('front.panel.accounts.index'))
            ->assertSee($account->username);
    }

    public function testUnactivatedAccountShownInList()
    {
        $account = Account::factory()->unactivated()->create();

        $this->actingAs($this->adminAccount())
            ->get(route('front.panel.accounts.index'))
            ->assertSee($account->username);
    }
}
