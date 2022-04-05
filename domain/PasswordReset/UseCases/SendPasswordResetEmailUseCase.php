<?php

namespace Domain\PasswordReset\UseCases;

use App\Entities\Models\Eloquent\Account;
use App\Entities\Notifications\AccountPasswordResetNotification;
use Domain\PasswordReset\Repositories\AccountPasswordResetRepository;
use Library\Tokens\TokenGenerator;

final class SendPasswordResetEmailUseCase
{
    public function __construct(
        private AccountPasswordResetRepository $passwordResetRepository,
        private TokenGenerator $tokenGenerator,
    ) {}

    public function execute(Account $account, string $email)
    {
        $passwordReset = $this->passwordResetRepository->updateByEmailOrCreate(
            email: $email,
            token: $this->tokenGenerator->make(),
        );
        $account->notify(new AccountPasswordResetNotification($passwordReset));
    }
}
