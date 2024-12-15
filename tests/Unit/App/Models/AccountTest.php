<?php

namespace App\Models;

use Tests\TestCase;

class AccountTest extends TestCase
{
    public function test_is_admin()
    {
        $account = Account::factory()->create();
        $account->groups()->attach(Group::factory()->administrator()->create());

        $this->assertTrue($account->isAdmin());
        $this->assertFalse(Account::factory()->create()->isAdmin());
    }

    public function test_is_staff()
    {
        $account = Account::factory()->create();
        $account->groups()->attach(Group::factory()->staff()->create());

        $this->assertTrue($account->isStaff());
        $this->assertFalse(Account::factory()->create()->isStaff());
    }
}
