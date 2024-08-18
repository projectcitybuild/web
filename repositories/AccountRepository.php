<?php

namespace Repositories;

use App\Models\Account;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

/**
 * @deprecated
 */
class AccountRepository
{
    public function create(
        string $email,
        string $username,
        string $password,
        ?string $ip
    ): Account {
        return Account::create([
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
        return Account::where('email', $email)->first();
    }

    public function activate(Account $account)
    {
        $account->activated = true;
        $account->save();
    }

    public function touchLastLogin(Account $account, string $ip)
    {
        $account->last_login_ip = $ip;
        $account->last_login_at = Carbon::now();
        $account->save();
    }
}
