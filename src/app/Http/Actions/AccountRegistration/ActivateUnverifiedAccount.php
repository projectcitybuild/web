<?php

namespace App\Http\Actions\AccountRegistration;

use App\Entities\Accounts\Repositories\AccountRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

final class ActivateUnverifiedAccount
{
    private $accountRepository;

    public function __construct(AccountRepository $accountRepository) {
        $this->accountRepository = $accountRepository;
    }

    public function execute(string $email, string $ip)
    {
        $account = $this->accountRepository->getByEmail($email);
        if ($account === null) {
            throw new \Exception('This unactivated account does not exist');
        }

        if ($account->activated == true) {
            // Account already activated
            abort(410);
        }

        $account->activated = true;
        $account->save();
    }
}
