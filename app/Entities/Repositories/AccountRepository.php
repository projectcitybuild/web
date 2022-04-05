<?php

namespace App\Entities\Repositories;

use App\Entities\Models\Eloquent\Account;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

final class AccountRepository
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
        return Account::where('email', $email)
            ->first();
    }
}
