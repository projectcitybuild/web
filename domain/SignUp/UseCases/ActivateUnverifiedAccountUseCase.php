<?php

namespace Domain\SignUp\UseCases;

use App\Exceptions\Http\BadRequestException;
use Domain\SignUp\Exceptions\AccountAlreadyActivatedException;
use Repositories\AccountRepository;

final class ActivateUnverifiedAccountUseCase
{
    public function __construct(
        private AccountRepository $accountRepository
    ) {}

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
