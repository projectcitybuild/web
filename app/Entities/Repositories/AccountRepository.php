<?php

namespace App\Entities\Accounts\Repositories;

use App\Entities\Models\Account;
use App\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

/**
 * @deprecated Use Account model facade instead
 */
final class AccountRepository extends Repository
{
    protected $model = Account::class;

    public function create(
        string $email,
        string $username,
        string $password,
        ?string $ip
    ): Account {
        return $this->getModel()->create([
            'email' => $email,
            'username' => $username,
            'password' => Hash::make($password),
            'remember_token' => '',
            'last_login_ip' => $ip,
            'last_login_at' => Carbon::now(),
        ]);
    }

    public function getByEmail(string $email): ?Account
    {
        return $this->getModel()
            ->where('email', $email)
            ->first();
    }
}
