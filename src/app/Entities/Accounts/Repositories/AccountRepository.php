<?php

namespace App\Entities\Accounts\Repositories;

use App\Entities\Accounts\Models\Account;
use App\Repository;
use Carbon\Carbon;

final class AccountRepository extends Repository
{
    protected $model = Account::class;

    public function create(
        string $email,
        string $username,
        string $password,
        ?string $ip
    ) : Account
    {
        return $this->getModel()->create([
            'email'         => $email,
            'username'      => $username,
            'password'      => $password,
            'remember_token' => '',
            'last_login_ip' => $ip,
            'last_login_at' => Carbon::now()
        ]);
    }

    public function getByEmail(string $email) : ?Account
    {
        return $this->getModel()
            ->where('email', $email)
            ->first();
    }

    public function deleteUnactivatedOlderThan(Carbon $date)
    {
        return $this->getModel()
            ->where('activated', false)
            ->whereDate('updated_at', '<', $date)
            ->delete();
    }
}
