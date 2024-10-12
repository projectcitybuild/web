<?php

namespace App\Domains\Activation\UseCases;

use App\Core\Domains\SecureTokens\SecureTokenGenerator;
use App\Core\Support\Laravel\SignedURL\SignedURLGenerator;
use App\Domains\Activation\Exceptions\AccountAlreadyActivatedException;
use App\Domains\Activation\Notifications\AccountNeedsActivationNotification;
use App\Models\Account;
use App\Models\AccountActivation;

class SendActivationEmail
{
    public function __construct(
        private readonly SignedURLGenerator   $signedURLGenerator,
        private readonly SecureTokenGenerator $tokenGenerator,
    ) {}

    public function execute(Account $account): void
    {
        if ($account->activated) {
            throw new AccountAlreadyActivatedException();
        }

        $activation = AccountActivation::create([
            'account_id' => $account->getKey(),
            'token' => $this->tokenGenerator->make(),
            'expires_at' => now()->addDay(),
        ]);

        $activationURL = $this->signedURLGenerator->make(
            routeName: 'front.activate.verify',
            parameters: ['token' => $activation->token],
        );

        $account->notify(
            new AccountNeedsActivationNotification(activationURL: $activationURL)
        );
    }
}
