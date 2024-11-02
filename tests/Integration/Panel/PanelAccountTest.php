<?php

namespace Panel;

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
            ->get(route('manage.accounts.show', $account))
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
            ->put(route('manage.accounts.update', $account), $newData)
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
            ->get(route('manage.accounts.update', $account))
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
