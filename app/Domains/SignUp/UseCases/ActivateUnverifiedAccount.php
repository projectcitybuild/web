<?php

namespace App\Domains\SignUp\UseCases;

use App\Core\Data\Exceptions\BadRequestException;
use App\Domains\SignUp\Exceptions\AccountAlreadyActivatedException;
use Repositories\AccountRepository;

final class ActivateUnverifiedAccount
{
    public function __construct(
        private readonly AccountRepository $accountRepository
    ) {
    }

    public function execute(string $email)
    {
        if (empty($email)) {
            throw new BadRequestException(id: 'missing_email', message: 'Cannot activate account with no email');
        }

        $account = $this->accountRepository->getByEmail($email)
            ?? throw new \Exception('This unactivated account does not exist');

        if ($account->activated === true) {
            throw new AccountAlreadyActivatedException();
        }

        $this->accountRepository->activate($account);
    }
}
