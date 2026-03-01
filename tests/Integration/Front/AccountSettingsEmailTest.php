<?php

namespace Tests\Integration\Front;

use App\Domains\EmailChange\Notifications\VerifyNewEmailAddressNotification;
use App\Models\Account;
use App\Models\EmailChange;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class AccountSettingsEmailTest extends TestCase
{
    private $account;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

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
        Notification::assertNothingSent();

        $newEmail = 'valid@email.com';

        $this->submitEmailChange($newEmail);

        $this->assertDatabaseHas('account_email_changes', [
            'email' => $newEmail,
            'account_id' => $this->account->id,
        ]);

        Notification::assertSentTo(
            Notification::route('mail', $newEmail),
            VerifyNewEmailAddressNotification::class,
        );
    }

    public function test_deletes_existing_email_change()
    {
        EmailChange::create([
            'token' => 'test',
            'account_id' => $this->account->id,
            'email' => 'foo@bar.com',
            'expires_at' => now()->addDay(),
        ]);
        $this->assertDatabaseHas('account_email_changes', [
            'token' => 'test',
            'account_id' => $this->account->id,
            'email' => 'foo@bar.com',
        ]);

        $newEmail = 'valid@email.com';

        $this->submitEmailChange($newEmail);

        $this->assertDatabaseMissing('account_email_changes', [
            'token' => 'test',
            'account_id' => $this->account->id,
            'email' => 'foo@bar.com',
        ]);
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
