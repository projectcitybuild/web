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

        $account->notify(new AccountActivationNotification($account));
    }
}
