<?php

namespace Tests\Integration\Feature;

use App\Domains\Panel\Data\PanelGroupScope;
use App\Models\Account;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\IntegrationTestCase;

class PanelAccountTest extends IntegrationTestCase
{
    use WithFaker;

    private Account $admin;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->adminAccount(scopes: [
            PanelGroupScope::ACCESS_PANEL,
            PanelGroupScope::MANAGE_ACCOUNTS,
        ]);
    }

    public function test_account_details_shown()
    {
        $this->withoutExceptionHandling();

        $account = Account::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('front.panel.accounts.show', $account))
            ->assertOk()
            ->assertSee($account->username);
    }

    public function test_account_details_change()
    {
        $account = Account::factory()->create();

        $newData = [
            'email' => $this->faker->email,
            'username' => $this->faker->userName,
        ];

        $this->actingAs($this->admin)
            ->withoutExceptionHandling()
            ->put(route('front.panel.accounts.update', $account), $newData)
            ->assertRedirect();

        $this->assertDatabaseHas('accounts', $newData);
    }

    public function test_unauthorised_without_scope()
    {
        $account = Account::factory()->create();

        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::ACCESS_PANEL,
        ]);

        $this->actingAs($admin)
            ->get(route('front.panel.accounts.update', $account))
            ->assertUnauthorized();
    }

    public function test_unauthorised_without_panel_access()
    {
        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::MANAGE_ACCOUNTS,
        ]);

        $this->actingAs($admin)
            ->get(route('front.panel.accounts.index'))
            ->assertUnauthorized();
    }
}
