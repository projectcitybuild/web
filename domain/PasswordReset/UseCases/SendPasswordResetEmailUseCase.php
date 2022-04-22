<?php

namespace Domain\PasswordReset\UseCases;

use Domain\PasswordReset\PasswordResetURLGenerator;
use Domain\PasswordReset\Repositories\AccountPasswordResetRepository;
use Entities\Models\Eloquent\Account;
use Entities\Notifications\AccountPasswordResetNotification;
use Library\SignedURL\SignedURLGenerator;
use Library\Tokens\TokenGenerator;

final class SendPasswordResetEmailUseCase
{
    public function __construct(
        private AccountPasswordResetRepository $passwordResetRepository,
        private TokenGenerator $tokenGenerator,
        private SignedURLGenerator $signedURLGenerator,
    ) {}

    public function execute(Account $account, string $email): void
    {
        $token = $this->tokenGenerator->make();
        $this->passwordResetRepository->updateByEmailOrCreate(
            email: $email,
            token: $token,
        );
        $passwordResetURL = $this->signedURLGenerator->makeTemporary(
            routeName: 'front.password-reset.edit',
            expiresAt: now()->addMinutes(20),
            parameters: ['token' => $token],
        );
        $account->notify(
            new AccountPasswordResetNotification(passwordResetURL: $passwordResetURL)
        );
    }
}
