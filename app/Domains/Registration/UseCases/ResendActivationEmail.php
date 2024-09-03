<?php

namespace App\Domains\Registration\UseCases;

use App\Core\Support\Laravel\SignedURL\SignedURLGenerator;
use App\Domains\Registration\Exceptions\AccountAlreadyActivatedException;
use App\Domains\Registration\Notifications\AccountActivationNotification;
use Repositories\AccountRepository;

/**
 * @final
 */
class ResendActivationEmail
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly SignedURLGenerator $signedURLGenerator,
    ) {
    }

    public function execute(string $email)
    {
        $account = $this->accountRepository->getByEmail($email);
        if ($account === null) {
            return;
        }

        if ($account->activated) {
            throw new AccountAlreadyActivatedException();
        }

        $activationURL = $this->signedURLGenerator->makeTemporary(
            routeName: 'front.activate.verify',
            expiresAt: now()->addDay(),
            parameters: ['email' => $account->email],
        );
        $account->notify(
            new AccountActivationNotification(activationURL: $activationURL)
        );
    }
}
