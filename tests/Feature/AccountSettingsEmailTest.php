<?php

namespace Tests\Feature;

use App\Entities\Models\Eloquent\Account;
use App\Entities\Notifications\AccountEmailChangeVerifyNotification;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AccountSettingsEmailTest extends TestCase
{
    use WithFaker;

    private $account;

    protected function setUp(): void
    {
        parent::setUp();
        $this->account = Account::factory()->create();
    }

    /**
     * @param $newEmail
     */
    private function submitEmailChange($newEmail): \Illuminate\Testing\TestResponse
    {
        return $this->actingAs($this->account)->post(route('front.account.settings.email'), [
            'email' => $newEmail,
        ]);
    }

    public function testChangeEmailAddress()
    {
        Notification::fake();
        Notification::assertNothingSent();

        $newEmail = $this->faker->email;
        $oldEmail = $this->account->email;

        $this->submitEmailChange($newEmail);

        $this->assertDatabaseHas('account_email_changes', [
            'email_previous' => $oldEmail,
            'email_new' => $newEmail,
        ]);

        // Test notification to old email
        Notification::assertSentTo($this->account, AccountEmailChangeVerifyNotification::class);
        // Test notification to new email
        Notification::assertSentTo(Notification::route('mail', $newEmail), AccountEmailChangeVerifyNotification::class);
    }

    public function testCantChangeEmailToExistingEmail()
    {
        $otherAccount = Account::factory()->create();

        $this->submitEmailChange($otherAccount->email)
            ->assertSessionHasErrors();
    }

    public function testCantSubmitEmptyEmail()
    {
        $this->submitEmailChange('')
            ->assertSessionHasErrors();
    }

    public function testCantSubmitInvalidEmail()
    {
        $this->submitEmailChange('test')
            ->assertSessionHasErrors();
    }

    public function testCantSubmitSameEmail()
    {
        $this->submitEmailChange($this->account->email)
            ->assertSessionHasErrors();
    }
}
