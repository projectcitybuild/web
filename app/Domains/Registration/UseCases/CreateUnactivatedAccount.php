<?php

namespace App\Domains\Registration\UseCases;

use App\Core\Domains\Groups\GroupsManager;
use App\Domains\Registration\Events\AccountCreated;
use App\Models\Account;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CreateUnactivatedAccount
{
    public function __construct(
        private readonly GroupsManager $groupsManager,
    ) {}

    public function execute(
        string $email,
        string $username,
        string $password,
        string $ip,
    ) {
        $account = Account::create([
            'email' => $email,
            'username' => $username,
            'password' => Hash::make($password),
            'remember_token' => '',
            'last_login_ip' => $ip,
            'last_login_at' => Carbon::now(),
        ]);

        $this->groupsManager->addToDefaultGroup($account);

        AccountCreated::dispatch($account);

        Auth::login($account);
    }
}
