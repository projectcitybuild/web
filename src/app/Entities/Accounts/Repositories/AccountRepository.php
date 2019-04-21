<?php
namespace App\Entities\Accounts\Repositories;

use App\Entities\Accounts\Models\Account;
use App\Repository;
use Carbon\Carbon;

class AccountRepository extends Repository
{
    protected $model = Account::class;

    public function create(
        string $email,
        string $password,
        ?string $ip,
        Carbon $createdAt
    ) : Account {
        return $this->getModel()->create([
            'email'         => $email,
            'password'      => $password,
            'remember_token' => '',
            'last_login_ip' => $ip,
            'last_login_at' => Carbon::now(),
            'created_at'    => $createdAt,
            'updated_at'    => Carbon::now(),
        ]);
    }

    public function getByEmail(string $email) : ?Account
    {
        return $this->getModel()
            ->where('email', $email)
            ->first();
    }
}
