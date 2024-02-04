<?php

namespace Domain\PasswordReset\UseCases;

use Entities\Models\Eloquent\Account;
use Entities\Notifications\AccountPasswordResetNotification;
use Library\SignedURL\SignedURLGenerator;
use Library\Tokens\TokenGenerator;
use Repositories\AccountPasswordResetRepository;

final class SendPasswordResetEmail
{
    public function __construct(
        private readonly AccountPasswordResetRepository $passwordResetRepository,
        private readonly TokenGenerator $tokenGenerator,
        private readonly SignedURLGenerator $signedURLGenerator,
    ) {
    }

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
