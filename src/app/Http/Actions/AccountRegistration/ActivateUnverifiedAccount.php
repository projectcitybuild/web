<?php

namespace App\Http\Actions\AccountRegistration;

use App\Entities\Accounts\Repositories\UnactivatedAccountRepository;
use App\Entities\Accounts\Repositories\AccountRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

final class ActivateUnverifiedAccount
{
    private $unactivatedAccountRepository;
    private $accountRepository;
    
    public function __construct(UnactivatedAccountRepository $unactivatedAccountRepository, AccountRepository $accountRepository) {
        $this->unactivatedAccountRepository = $unactivatedAccountRepository;
        $this->accountRepository = $accountRepository;
    }

    public function execute(string $email, string $ip)
    {
        $unactivatedAccount = $this->unactivatedAccountRepository->getByEmail($email);
        if ($unactivatedAccount === null) {
            throw new \Exception('This unactivated account does not exist');
        }

        $accountByEmail = $this->accountRepository->getByEmail($email);
        if ($accountByEmail) {
            // Account already activated
            abort(410);
        }

        DB::beginTransaction();
        try {
            $this->accountRepository->create(
                $unactivatedAccount->email,
                $unactivatedAccount->username,
                $unactivatedAccount->password,
                $ip,
                Carbon::now()
            );

            $unactivatedAccount->delete();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}