<?php

namespace Tests\Unit\Domain\PasswordReset\UseCases;

use App\Exceptions\Http\NotFoundException;
use Domain\PasswordReset\UseCases\ResetAccountPassword;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\AccountPasswordReset;
use Entities\Notifications\AccountPasswordResetCompleteNotification;
use Illuminate\Support\Facades\Notification;
use Repositories\AccountPasswordResetRepository;
use Repositories\AccountRepository;
use Tests\TestCase;

class ResetAccountPasswordUseCaseTest extends TestCase
{
    private AccountRepository $accountRepository;
    private AccountPasswordResetRepository $passwordResetRepository;
    private ResetAccountPassword $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->accountRepository = \Mockery::mock(AccountRepository::class);
        $this->passwordResetRepository = \Mockery::mock(AccountPasswordResetRepository::class);

        $this->useCase = new ResetAccountPassword(
            passwordResetRepository: $this->passwordResetRepository,
            accountRepository: $this->accountRepository,
        );

        Notification::fake();
    }

    public function test_throws_exception_if_token_not_found()
    {
        $this->passwordResetRepository
            ->shouldReceive('firstByToken')
            ->andReturnNull();

        $this->expectException(NotFoundException::class);

        $this->useCase->execute(
            token: 'token',
            newPassword: 'new_password'
        );
    }

    public function test_throws_exception_if_account_not_found()
    {
        $passwordReset = AccountPasswordReset::factory()->create();

        $this->passwordResetRepository
            ->shouldReceive('firstByToken')
            ->andReturn($passwordReset);

        $this->accountRepository
            ->shouldReceive('getByEmail')
            ->andReturnNull();

        $this->expectException(NotFoundException::class);

        $this->useCase->execute(
            token: $passwordReset->token,
            newPassword: 'new_password',
        );
    }

    public function test_updates_password()
    {
        $passwordReset = AccountPasswordReset::factory()->create();
        $account = Account::factory()->create();
        $newPassword = 'new_password';

        $this->passwordResetRepository
            ->shouldReceive('firstByToken')
            ->with($passwordReset->token)
            ->andReturn($passwordReset);

        $this->accountRepository
            ->shouldReceive('getByEmail')
            ->with($passwordReset->email)
            ->andReturn($account);

        $this->passwordResetRepository
            ->shouldReceive('delete')
            ->with($passwordReset);

        $this->useCase->execute(
            token: $passwordReset->token,
            newPassword: $newPassword,
        );
    }

    public function test_sends_notification()
    {
        Notification::assertNothingSent();

        $passwordReset = AccountPasswordReset::factory()->create();
        $account = Account::factory()->create();

        $this->passwordResetRepository
            ->shouldReceive('firstByToken')
            ->with($passwordReset->token)
            ->andReturn($passwordReset);

        $this->accountRepository
            ->shouldReceive('getByEmail')
            ->with($passwordReset->email)
            ->andReturn($account);

        $this->passwordResetRepository->shouldReceive('delete');

        $this->useCase->execute(
            token: $passwordReset->token,
            newPassword: 'new_password',
        );

        Notification::assertSentTo($account, AccountPasswordResetCompleteNotification::class);
    }
}
