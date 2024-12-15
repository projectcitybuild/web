<?php

namespace Tests\Integration\Panel;

use App\Models\Account;
use Tests\TestCase;

class ManageAccountListTest extends TestCase
{
    public function test_account_shown_in_list()
    {
        $account = Account::factory()->create();

        $this->actingAs($this->adminAccount())
            ->get(route('manage.accounts.index'))
            ->assertSee($account->username);
    }

    public function test_unactivated_account_shown_in_list()
    {
        $account = Account::factory()->unactivated()->create();

        $this->actingAs($this->adminAccount())
            ->get(route('manage.accounts.index'))
            ->assertSee($account->username);
    }

    public function test_forbidden_unless_admin()
    {
        $this->actingAs($this->adminAccount())
            ->get(route('manage.accounts.index'))
            ->assertSuccessful();

        $this->actingAs($this->staffAccount())
            ->get(route('manage.accounts.index'))
            ->assertForbidden();

        $this->actingAs(Account::factory()->create())
            ->get(route('manage.accounts.index'))
            ->assertForbidden();
    }
}
