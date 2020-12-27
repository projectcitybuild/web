<?php

namespace App\Http\Actions\AccountRegistration;

use App\Entities\Accounts\Repositories\AccountRepository;

final class ActivateUnverifiedAccount
{
    private $accountRepository;

    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function execute(string $email, string $ip)
    {
        $account = $this->accountRepository->getByEmail($email);

        if ($account === null) {
            throw new \Exception('This unactivated account does not exist');
        }

        if ($account->activated === true) {
            // Account already activated
            abort(410);
        }

        $account->activated = true;
        $account->save();
    }
}
