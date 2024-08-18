<?php

namespace App\Domains\SignUp\UseCases;

use App\Core\Domains\SignedURL\SignedURLGenerator;
use App\Domains\SignUp\Notifications\AccountActivationNotification;
use Repositories\AccountRepository;
use Shared\Groups\GroupsManager;

/**
 * @final
 */
class CreateUnactivatedAccount
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly GroupsManager $groupsManager,
        private readonly SignedURLGenerator $signedURLGenerator,
    ) {
    }

    public function execute(
        string $email,
        string $username,
        string $password,
        string $ip,
    ) {
        $account = $this->accountRepository->create(
            email: $email,
            username: $username,
            password: $password,
            ip: $ip,
        );

        $this->groupsManager->addToDefaultGroup($account);

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
