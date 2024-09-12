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
    ) {}

    public function execute(Account $account, string $email): void
    {
        $token = $this->tokenGenerator->make();

        PasswordReset::create([
            'email' => $email,
            'token' => $token,
            'account_id' => $account->getKey(),
            'created_at' => Carbon::now(),
            'expires_at' => now()->addMinutes(20),
        ]);

        $passwordResetURL = $this->signedURLGenerator->make(
            routeName: 'front.password-reset.edit',
            parameters: ['token' => $token],
        );

        $account->notify(
            new AccountPasswordResetNotification(passwordResetURL: $passwordResetURL)
        );
    }
}
