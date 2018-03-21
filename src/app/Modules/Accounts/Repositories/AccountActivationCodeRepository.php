<?php
namespace App\Modules\Accounts\Repositories;

use App\Modules\Accounts\Models\AccountActivationCode;
use App\Shared\Repository;
use Carbon\Carbon;


class AccountActivationCodeRepository extends Repository {

    protected $model = AccountActivationCode::class;

    public function create(
        string $token,
        string $email, 
        string $password,
        Carbon $expiresAt
    ) : AccountActivationCode {

        return $this->getModel()->create([
            'email'         => $email,
            'password'      => $password,
            'token'         => $token,
            'expires_at'    => $expiresAt,
        ]);
    }

    public function delete(int $accountActivationCodeId) : int {
        return $this->getModel()
            ->where('account_activation_id', $accountActivationCodeId)
            ->delete();
    }
    
}