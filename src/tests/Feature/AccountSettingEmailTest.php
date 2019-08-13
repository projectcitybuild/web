<?php


namespace Tests\Feature;


use App\Entities\Accounts\Models\Account;
use App\Entities\Accounts\Notifications\AccountEmailChangeVerifyNotification;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AccountSettingEmailTest extends TestCase
{
    use WithFaker;

    private $account;

    protected function setUp(): void
    {
        parent::setUp();
        $this->account = factory(Account::class)->create();
    }

    /**
     * @param $newEmail
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    private function submitEmailChange($newEmail): \Illuminate\Foundation\Testing\TestResponse
    {
        return $this->actingAs($this->account)->post(route('front.account.settings.email'), [
            'email' => $newEmail
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
            'email_new' => $newEmail
        ]);

        // Test notification to old email
        Notification::assertSentTo($this->account, AccountEmailChangeVerifyNotification::class);
        // Test notification to new email
        Notification::assertSentTo(Notification::route('mail', $newEmail), AccountEmailChangeVerifyNotification::class);
    }

    public function testCantChangeEmailToExistingEmail()
    {
        $otherAccount = factory(Account::class)->create();

        $this->submitEmailChange($otherAccount->email)
            ->assertSessionHasErrors();
    }

    public function testCantSubmitEmptyEmail()
    {
        $this->submitEmailChange("")
            ->assertSessionHasErrors();
    }

    public function testCantSubmitInvalidEmail()
    {
        $this->submitEmailChange("test")
            ->assertSessionHasErrors();
    }

    public function testCantSubmitSameEmail()
    {
        $this->submitEmailChange($this->account->email)
            ->assertSessionHasErrors();
    }
}
