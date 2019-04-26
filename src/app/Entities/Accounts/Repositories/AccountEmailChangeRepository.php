<?php
namespace App\Entities\Accounts\Repositories;

use App\Entities\Accounts\Models\AccountEmailChange;
use App\Repository;
use Carbon\Carbon;

class AccountEmailChangeRepository extends Repository
{
    protected $model = AccountEmailChange::class;

    public function create(int $accountId,
                           string $token,
                           string $previousEmail,
                           string $newEmail) : AccountEmailChange 
    {
        return $this->getModel()
            ->create([
                'account_id'            => $accountId,
                'token'                 => $token,
                'email_previous'        => $previousEmail,
                'email_new'             => $newEmail,
                'is_previous_confirmed' => false,
                'is_new_confirmed'      => false,
            ]);
    }

    public function getByToken(string $token) : ?AccountEmailChange
    {
        return $this->getModel()
            ->where('token', $token)
            ->first();
    }
}
