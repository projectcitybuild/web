<?php

namespace App\Domains\Activation\UseCases;

use App\Core\Domains\Tokens\TokenGenerator;
use App\Core\Support\Laravel\SignedURL\SignedURLGenerator;
use App\Domains\Activation\Exceptions\AccountAlreadyActivatedException;
use App\Domains\Activation\Notifications\AccountNeedsActivationNotification;
use App\Models\Account;
use App\Models\AccountActivation;

class SendActivationEmail
{
    public function __construct(
        private readonly SignedURLGenerator $signedURLGenerator,
        private readonly TokenGenerator $tokenGenerator,
    ) {}

    public function execute(Account $account): void
    {
        if ($account->activated) {
            throw new AccountAlreadyActivatedException();
        }

        $activation = AccountActivation::create([
            'account_id' => $account->getKey(),
            'token' => $this->tokenGenerator->make(),
        ]);

        $activationURL = $this->signedURLGenerator->makeTemporary(
            routeName: 'front.activate.verify',
            expiresAt: now()->addDay(),
            parameters: ['token' => $activation->token],
        );

        $account->notify(
            new AccountNeedsActivationNotification(activationURL: $activationURL)
        );
    }
}
