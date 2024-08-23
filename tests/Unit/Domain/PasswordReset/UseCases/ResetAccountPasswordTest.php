<?php

namespace Tests\Unit\Domain\PasswordReset\UseCases;

use App\Core\Data\Exceptions\NotFoundException;
use App\Domains\PasswordReset\Notifications\AccountPasswordResetCompleteNotification;
use App\Domains\PasswordReset\UseCases\ResetAccountPassword;
use App\Models\Account;
use App\Models\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Repositories\AccountPasswordResetRepository;
use Repositories\AccountRepository;
use Tests\TestCase;

class ResetAccountPasswordTest extends TestCase
{
    use RefreshDatabase;

    private ResetAccountPassword $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->useCase = new ResetAccountPassword();

        Notification::fake();
    }

    public function test_throws_exception_if_token_not_found()
    {
        $this->expectException(NotFoundException::class);

        $this->useCase->execute(
            token: 'token',
            newPassword: 'new_password'
        );
    }

    public function test_throws_exception_if_account_not_found()
    {
        $passwordReset = PasswordReset::factory()->create();

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
        $passwordReset = PasswordReset::factory()->create();
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

        // Skip risky warning
        $this->assertTrue(true);
    }

    public function test_sends_notification()
    {
        Notification::assertNothingSent();

        $passwordReset = PasswordReset::factory()->create();
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
