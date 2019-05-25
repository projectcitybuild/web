<?php
namespace App\Entities\Accounts\Repositories;

use App\Entities\Accounts\Models\UnactivatedAccount;
use App\Repository;
use Carbon\Carbon;

class UnactivatedAccountRepository extends Repository
{
    protected $model = UnactivatedAccount::class;

    public function create(
        string $email,
        string $username,
        string $password
    ) : UnactivatedAccount {
        return $this->getModel()->create([
            'email'         => $email,
            'username'      => $username,
            'password'      => $password
        ]);
    }

    public function delete(int $accountActivationCodeId) : int
    {
        return $this->getModel()
            ->where('account_activation_id', $accountActivationCodeId)
            ->delete();
    }

    public function deleteExpired() : int
    {
        return $this->getModel()
            ->whereDate('expires_at', '>=', Carbon::now())
            ->delete();
    }

    public function getByEmail(string $email) : ?UnactivatedAccount
    {
        return $this->getModel()
            ->where('email', $email)
            ->first();
    }

    public function deleteOlderThan(Carbon $date)
    {
        return $this->getModel()
            ->whereDate('updated_at', '<', $date)
            ->delete();
    }
}
