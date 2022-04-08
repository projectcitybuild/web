<?php

namespace Domain\PasswordReset\UseCases;

use App\Entities\Models\Eloquent\Account;
use App\Entities\Notifications\AccountPasswordResetNotification;
use Domain\PasswordReset\PasswordResetURLGenerator;
use Domain\PasswordReset\Repositories\AccountPasswordResetRepository;
use Library\Tokens\TokenGenerator;

final class SendPasswordResetEmailUseCase
{
    public function __construct(
        private AccountPasswordResetRepository $passwordResetRepository,
        private TokenGenerator $tokenGenerator,
        private PasswordResetURLGenerator $passwordResetURLGenerator,
    ) {}

    public function execute(Account $account, string $email)
    {
        $token = $this->tokenGenerator->make();
        $this->passwordResetRepository->updateByEmailOrCreate(
            email: $email,
            token: $token,
        );
        $account->notify(
            new AccountPasswordResetNotification(
                passwordResetURL: $this->passwordResetURLGenerator->make(token: $token)
            )
        );
    }
}
