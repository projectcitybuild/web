<?php

namespace App\Domains\PasswordReset\UseCases;

use App\Core\Domains\Tokens\TokenGenerator;
use App\Core\Support\Laravel\SignedURL\SignedURLGenerator;
use App\Domains\PasswordReset\Notifications\AccountPasswordResetNotification;
use App\Models\Account;
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