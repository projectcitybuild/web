<?php

namespace App\Domains\EditAccount\Actions;

use App\Models\Account;
use Illuminate\Support\Facades\Hash;

final class UpdateUserPassword
{
    public function call(Account $user, String $newPassword): void
    {
        $newPassword = Hash::make($newPassword);
        $user->forceFill([
            'password' => $newPassword,
            'password_changed_at' => now(),
        ]);
        $user->save();
    }
}
