<?php

namespace App\Actions\Me;

use App\Models\Eloquent\Account;
use Illuminate\Support\Facades\Hash;

final class UpdateUserPassword
{
    public function update(Account $user, String $newPassword): void
    {
        $newPassword = Hash::make($newPassword);
        $user->forceFill([
            'password' => $newPassword,
            'password_changed_at' => now(),
        ]);
        $user->save();
    }
}
