<?php

namespace App\Domains\Registration\Actions;

use App\Models\Account;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

class CreateAccount
{
    public function call(
        string $username,
        string $email,
        string $password,
    ): void {
        // TODO: move account existence check to here

        $account = Account::create([
            'username' => $username,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        // Email sending handled by Laravel
        // https://laravel.com/docs/11.x/verification
        event(new Registered($account));
    }
}
