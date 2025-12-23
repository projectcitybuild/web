<?php

namespace App\Domains\PasswordReset\UseCases;

use App\Domains\PasswordReset\Notifications\AccountPasswordResetCompleteNotification;
use App\Models\Account;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\DB;

final class ResetAccountPassword
{
    public function execute(string $token, string $newPassword)
    {
        $passwordReset = PasswordReset::whereToken($token)->firstOrFail();

        $account = Account::whereEmail($passwordReset->email)->firstOrFail();

        DB::transaction(function () use ($account, $passwordReset, $newPassword) {
            $account->updatePassword($newPassword);
            $passwordReset->delete();

            PasswordReset::where('account_id', $account->getKey())
                ->update(['expires_at' => now()]);
        });

        $account->notify(new AccountPasswordResetCompleteNotification);
    }
}
