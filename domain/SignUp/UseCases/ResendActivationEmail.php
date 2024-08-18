<?php

namespace Domain\SignUp\UseCases;

use App\Core\Domains\SignedURL\SignedURLGenerator;
use Domain\SignUp\Exceptions\AccountAlreadyActivatedException;
use Entities\Notifications\AccountActivationNotification;
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
            routeName: 'front.register.activate',
            expiresAt: now()->addDay(),
            parameters: ['email' => $account->email],
        );
        $account->notify(
            new AccountActivationNotification(activationURL: $activationURL)
        );
    }
}
