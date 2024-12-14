<?php

namespace Panel;

use App\Domains\Manage\Data\PanelGroupScope;
use App\Models\Account;
use Tests\IntegrationTestCase;

class PanelAccountListTest extends IntegrationTestCase
{
    private Account $admin;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->adminAccount(scopes: [
            PanelGroupScope::ACCESS_PANEL,
            PanelGroupScope::MANAGE_ACCOUNTS,
        ]);
    }

    public function test_account_shown_in_list()
    {
        $account = Account::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('manage.accounts.index'))
            ->assertSee($account->username);
    }

    public function test_unactivated_account_shown_in_list()
    {
        $account = Account::factory()->unactivated()->create();

        $this->actingAs($this->admin)
            ->get(route('manage.accounts.index'))
            ->assertSee($account->username);
    }

    public function test_unauthorised_without_scope()
    {
        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::ACCESS_PANEL,
        ]);

        $this->actingAs($admin)
            ->get(route('manage.accounts.index'))
            ->assertUnauthorized();
    }

    public function test_unauthorised_without_panel_access()
    {
        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::MANAGE_ACCOUNTS,
        ]);

        $this->actingAs($admin)
            ->get(route('manage.accounts.index'))
            ->assertUnauthorized();
    }
}
