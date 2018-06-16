<?php
namespace App\Modules\Accounts\Repositories;

use App\Modules\Accounts\Models\UnactivatedAccount;
use App\Core\Repository;
use Carbon\Carbon;


class UnactivatedAccountRepository extends Repository {

    protected $model = UnactivatedAccount::class;

    public function create(
        string $email, 
        string $password
    ) : UnactivatedAccount {

        return $this->getModel()->create([
            'email'         => $email,
            'password'      => $password,
        ]);
    }

    public function delete(int $accountActivationCodeId) : int {
        return $this->getModel()
            ->where('account_activation_id', $accountActivationCodeId)
            ->delete();
    }

    public function deleteExpired() : int {
        return $this->getModel()
            ->whereDate('expires_at', '>=', Carbon::now())
            ->delete();
    }

    public function getByEmail(string $email) : ?UnactivatedAccount {
        return $this->getModel()
            ->where('email', $email)
            ->first();
    }
    
}