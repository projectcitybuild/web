<?php

namespace Domain\SignUp\UseCases;

use App\Entities\Notifications\AccountActivationNotification;
use App\Entities\Repositories\AccountRepository;
use Shared\Groups\GroupsManager;

/**
 * @final
 */
class CreateUnactivatedAccountUseCase
{
    public function __construct(
        private AccountRepository $accountRepository,
        private GroupsManager $groupsManager,
    ) {}

    public function execute(
        string $email,
        string $username,
        string $passord,
        string $ip,
    ) {
        $account = $this->accountRepository->create(
            email: $email,
            username: $username,
            password: $passord,
            ip: $ip,
        );

        $this->groupsManager->addToDefaultGroup();

        $account->notify(new AccountActivationNotification($account));
    }
}
