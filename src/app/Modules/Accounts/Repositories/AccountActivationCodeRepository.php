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
            'is_used'       => false,
            'expires_at'    => $expiresAt,
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

    public function getByToken(string $token) : ?AccountActivationCode {
        return $this->getModel()
            ->where('token', $token)
            ->first();
    }
    
}