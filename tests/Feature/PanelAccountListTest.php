<?php

namespace Tests\Feature;

use Entities\Models\Eloquent\Account;
use Entities\Models\PanelGroupScope;
use Tests\E2ETestCase;

class PanelAccountListTest extends E2ETestCase
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
            ->get(route('front.panel.accounts.index'))
            ->assertSee($account->username);
    }

    public function test_unactivated_account_shown_in_list()
    {
        $account = Account::factory()->unactivated()->create();

        $this->actingAs($this->admin)
            ->get(route('front.panel.accounts.index'))
            ->assertSee($account->username);
    }

    public function test_forbidden_without_scope()
    {
        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::ACCESS_PANEL,
        ]);

        $this->actingAs($admin)
            ->get(route('front.panel.accounts.index'))
            ->assertForbidden();
    }

    public function test_forbidden_without_panel_access()
    {
        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::MANAGE_ACCOUNTS,
        ]);

        $this->actingAs($admin)
            ->get(route('front.panel.accounts.index'))
            ->assertForbidden();
    }
}
