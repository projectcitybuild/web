<?php

namespace App\Domains\PasswordReset\UseCases;

use App\Core\Domains\Tokens\TokenGenerator;
use App\Core\Support\Laravel\SignedURL\SignedURLGenerator;
use App\Domains\PasswordReset\Notifications\AccountPasswordResetNotification;
use App\Models\Account;
use App\Models\PasswordReset;
use Illuminate\Support\Carbon;

final class SendPasswordResetEmail
{
    public function __construct(
        private readonly TokenGenerator $tokenGenerator,
        private readonly SignedURLGenerator $signedURLGenerator,
    ) {
    }

    public function execute(Account $account, string $email): void
    {
        $token = $this->tokenGenerator->make();

        PasswordReset::updateOrCreate(
            attributes: [
                'email' => $email,
            ],
            values: [
                'token' => $token,
                'created_at' => Carbon::now(),
            ],
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
