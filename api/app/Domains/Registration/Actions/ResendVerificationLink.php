<?php

namespace App\Domains\Registration\Actions;

use App\Models\Account;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

class ResendVerificationLink
{
    public function call(Account $account): void
    {
        if ($account->hasVerifiedEmail()) {
            abort(code: 409);
        }
        $account->sendEmailVerificationNotification();
    }
}
