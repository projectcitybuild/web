<?php

namespace Tests\Feature;

use App\Entities\Models\Eloquent\Account;
use App\Http\Actions\SyncUserToDiscourse;
use Illuminate\Foundation\Testing\WithFaker;
use Shared\ExternalAccounts\Sync\ExternalAccountSync;
use Tests\TestCase;

class PanelAccountTest extends TestCase
{
    use WithFaker;

    public function testAccountDetailsShown()
    {
        $this->withoutExceptionHandling();

        $account = Account::factory()->create();

        $this->actingAs($this->adminAccount())
            ->get(route('front.panel.accounts.show', $account))
            ->assertOk()
            ->assertSee($account->username);
    }

    public function testAccountDetailsChange()
    {
        $account = Account::factory()->create();

        $newData = [
            'email' => $this->faker->email,
            'username' => $this->faker->userName,
        ];

        $this->mock(ExternalAccountSync::class, function ($mock) {
            $mock->shouldReceive('sync')->once();
        });

        $this->actingAs($this->adminAccount())
            ->withoutExceptionHandling()
            ->put(route('front.panel.accounts.update', $account), $newData)
            ->assertRedirect();

        $this->assertDatabaseHas('accounts', $newData);
    }
}
