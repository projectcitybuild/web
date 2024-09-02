<?php

namespace Feature\Auth;

use App\Core\Domains\Tokens\Adapters\StubTokenGenerator;
use App\Core\Support\Laravel\SignedURL\Adapters\StubSignedURLGenerator;
use App\Domains\PasswordReset\Notifications\AccountPasswordResetNotification;
use App\Domains\PasswordReset\UseCases\SendPasswordResetEmail;
use App\Models\Account;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    private Account $account;
    private SendPasswordResetEmail $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->account = Account::factory()->create();

        $this->useCase = new SendPasswordResetEmail(
            tokenGenerator: new StubTokenGenerator('token'),
            signedURLGenerator: new StubSignedURLGenerator(outputURL: 'url'),
        );

        Notification::fake();
    }

    public function test_user_can_request_password_reset_email()
    {
        Notification::assertNothingSent();

        $this->post(
            uri: route(name: 'front.password-reset.store'),
            data: ['email' => $this->account->email]
        )->assertSessionHasNoErrors();

        Notification::assertSentTo($this->account, AccountPasswordResetNotification::class);
    }

    public function test_user_can_change_password()
    {
        $reset = PasswordReset::factory()->create([
            'email' => $this->account->email,
        ]);

        $originalPassword = $this->account->password;

        $this->patch(
            uri: route(name: 'front.password-reset.update'),
            data: [
                'password_token' => $reset->token,
                'password' => 'new_password',
                'password_confirm' => 'new_password',
            ],
        )->assertSessionHasNoErrors();

        $this->assertNotEquals(
            $originalPassword,
            Account::find($this->account->getKey())->password,
        );
    }
}
