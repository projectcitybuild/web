<?php

namespace App\Domains\Accounts\Actions;

use App\Models\Account;
use Illuminate\Support\Facades\Hash;

final class UpdateUserPassword
{
    public function call(Account $user, string $newPassword): void
    {
        $newPassword = Hash::make($newPassword);
        $user->forceFill([
            'password' => $newPassword,
            'password_changed_at' => now(),
        ]);
        $user->save();
    }
}
