<?php

namespace Tests\Integration\Feature;

use Domain\PasswordReset\UseCases\SendPasswordResetEmailUseCase;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\AccountPasswordReset;
use Entities\Notifications\AccountPasswordResetNotification;
use Illuminate\Support\Facades\Notification;
use Library\SignedURL\Adapters\StubSignedURLGenerator;
use Library\Tokens\Adapters\StubTokenGenerator;
use Repositories\AccountPasswordResetRepository;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    private Account $account;
    private AccountPasswordResetRepository $passwordResetRepository;
    private SendPasswordResetEmailUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->account = Account::factory()->create();
        $this->passwordResetRepository = \Mockery::mock(AccountPasswordResetRepository::class)->makePartial();

        $this->useCase = new SendPasswordResetEmailUseCase(
            passwordResetRepository: $this->passwordResetRepository,
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
        $reset = AccountPasswordReset::factory()->create([
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
