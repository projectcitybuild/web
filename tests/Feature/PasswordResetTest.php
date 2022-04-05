<?php

namespace Tests\Feature;

use App\Entities\Models\Eloquent\Account;
use App\Entities\Models\Eloquent\AccountPasswordReset;
use App\Entities\Notifications\AccountPasswordResetNotification;
use Domain\PasswordReset\Repositories\AccountPasswordResetRepository;
use Domain\PasswordReset\UseCases\SendPasswordResetEmailUseCase;
use Illuminate\Support\Facades\Notification;
use Library\Tokens\Adapters\StubTokenGenerator;
use Library\Tokens\TokenGenerator;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    private Account $account;
    private AccountPasswordResetRepository $passwordResetRepository;
    private SendPasswordResetEmailUseCase $useCase;
    private TokenGenerator $tokenGenerator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->account = Account::factory()->create();
        $this->passwordResetRepository = \Mockery::mock(AccountPasswordResetRepository::class)->makePartial();
        $this->tokenGenerator = new StubTokenGenerator('token');

        $this->useCase = new SendPasswordResetEmailUseCase(
            passwordResetRepository: $this->passwordResetRepository,
            tokenGenerator: $this->tokenGenerator,
        );

        $this->useCase->execute($this->account, $this->account->email);

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

    public function test_user_can_view_password_reset()
    {
        $reset = AccountPasswordReset::factory()->create();

        $this->get($reset->getPasswordResetUrl())
            ->assertSuccessful();
    }

    public function test_user_can_change_password()
    {
        $reset = AccountPasswordReset::factory()->create();

        $this->get($reset->getPasswordResetUrl())
            ->assertSuccessful();
    }
}
