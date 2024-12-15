<?php

namespace Tests\Integration\Panel;

use App\Models\Account;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ManageAccountTest extends TestCase
{
    use WithFaker;

    public function test_account_details_shown()
    {
        $this->withoutExceptionHandling();

        $account = Account::factory()->create();

        $this->actingAs($this->adminAccount())
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

        $this->actingAs($this->adminAccount())
            ->withoutExceptionHandling()
            ->put(route('manage.accounts.update', $account), $newData)
            ->assertRedirect();

        $this->assertDatabaseHas('accounts', $newData);
    }

    public function test_forbidden_unless_admin()
    {
        $account = Account::factory()->create();

        $this->actingAs($this->adminAccount())
            ->get(route('manage.accounts.update', $account))
            ->assertSuccessful();

        $this->actingAs($this->staffAccount())
            ->get(route('manage.accounts.update', $account))
            ->assertForbidden();

        $this->actingAs(Account::factory()->create())
            ->get(route('manage.accounts.update', $account))
            ->assertForbidden();
    }
}
