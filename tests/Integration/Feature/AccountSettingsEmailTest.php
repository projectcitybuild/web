<?php

namespace Tests\Integration\Feature;

use App\Models\Account;
use Domain\EmailChange\Notifications\VerifyNewEmailAddressNotification;
use Domain\EmailChange\Notifications\VerifyOldEmailAddressNotification;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\TestResponse;
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

    private function submitEmailChange(string $newEmail): TestResponse
    {
        return $this
            ->actingAs($this->account)
            ->post(
                uri: route('front.account.settings.email'),
                data: ['email' => $newEmail]
            );
    }

    public function test_change_email_address()
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
        Notification::assertSentTo(Notification::route('mail', $oldEmail), VerifyOldEmailAddressNotification::class);
        // Test notification to new email
        Notification::assertSentTo(Notification::route('mail', $newEmail), VerifyNewEmailAddressNotification::class);
    }

    public function test_cant_change_ema_il_to_existing_email()
    {
        $otherAccount = Account::factory()->create();

        $this->submitEmailChange($otherAccount->email)
            ->assertSessionHasErrors();
    }

    public function test_cant_submit_empty_email()
    {
        $this->submitEmailChange('')
            ->assertSessionHasErrors();
    }

    public function test_cant_submit_invalid_email()
    {
        $this->submitEmailChange('test')
            ->assertSessionHasErrors();
    }

    public function test_cant_submit_same_email()
    {
        $this->submitEmailChange($this->account->email)
            ->assertSessionHasErrors();
    }
}
