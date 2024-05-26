<?php

namespace App\Domains\PasswordReset\Actions;

use App\Models\Account;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SetNewPassword
{
    public function call(
        string $email,
        string $password,
        string $token,
    ): void {
        $credentials = [
          'email' => $email,
          'password' => $password,
          'password_confirmation' => $password,
          'token' => $token,
        ];
        $status = Password::reset(
            $credentials,
            fn ($account) => $this->resetPassword($account, newPassword: $password),
        );
        if ($status != Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }
    }

    private function resetPassword(Account $account, string $newPassword): void
    {
        $account->forceFill([
            'password' => Hash::make($newPassword),
            'remember_token' => Str::random(60),
        ])->save();

        event(new PasswordReset($account));
    }
}
