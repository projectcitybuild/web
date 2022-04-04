<?php

namespace Tests\Feature;

use App\Entities\Models\Eloquent\Account;
use Illuminate\Foundation\Testing\WithFaker;
use Library\Discourse\Api\DiscourseAdminApi;
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

    public function testChangeUsername()
    {
        $this->withoutExceptionHandling();

        $this->mock(DiscourseAdminApi::class, function ($mock) {
            $mock->shouldReceive('requestSSOSync')->once();
        });

        $newUsername = $this->faker->userName;
        $this->submitUsernameChange($newUsername)
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('accounts', [
            'email' => $this->account->email,
            'username' => $newUsername,
        ]);
    }

    public function testCantChangeToExistingUsername()
    {
        $otherAccount = Account::factory()->create();

        $this->submitUsernameChange($otherAccount->username)
            ->assertSessionHasErrors();
    }

    public function testCantSubmitEmptyUsername()
    {
        $this->submitUsernameChange('')
            ->assertSessionHasErrors();
    }

    public function testCantSubmitSameUsername()
    {
        $this->submitUsernameChange($this->account->username)
            ->assertSessionHasErrors();
    }
}
