<?php

namespace Domain\SignUp\UseCases;

use Domain\SignUp\Exceptions\AccountAlreadyActivatedException;
use Entities\Notifications\AccountActivationNotification;
use Entities\Repositories\AccountRepository;
use Library\SignedURL\SignedURLGenerator;

/**
 * @final
 */
class ResendActivationEmailUseCase
{
    public function __construct(
        private AccountRepository $accountRepository,
        private SignedURLGenerator $signedURLGenerator,
    ) {}

    public function execute(string $email)
    {
        $account = $this->accountRepository->getByEmail($email);
        if ($account === null) return;

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
