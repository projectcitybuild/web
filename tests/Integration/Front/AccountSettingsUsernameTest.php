<?php

namespace Tests\Integration\Front;

use App\Models\Account;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AccountSettingsUsernameTest extends TestCase
{
    use WithFaker;

    private $account;

    protected function setUp(): void
    {
        parent::setUp();
        $this->account = Account::factory()->create();
    }

    private function submitUsernameChange($newUsername): \Illuminate\Testing\TestResponse
    {
        return $this->actingAs($this->account)->post(route('front.account.settings.username'), [
            'username' => $newUsername,
        ]);
    }

    public function test_change_username()
    {
        $this->withoutExceptionHandling();

        $newUsername = $this->faker->userName;
        $this->submitUsernameChange($newUsername)
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('accounts', [
            'email' => $this->account->email,
            'username' => $newUsername,
        ]);
    }

    public function test_cant_change_to_existing_username()
    {
        $otherAccount = Account::factory()->create();

        $this->submitUsernameChange($otherAccount->username)
            ->assertSessionHasErrors();
    }

    public function test_cant_submit_empty_username()
    {
        $this->submitUsernameChange('')
            ->assertSessionHasErrors();
    }

    public function test_cant_submit_same_username()
    {
        $this->submitUsernameChange($this->account->username)
            ->assertSessionHasErrors();
    }
}
