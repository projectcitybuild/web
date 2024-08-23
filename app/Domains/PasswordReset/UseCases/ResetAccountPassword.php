<?php

namespace App\Domains\PasswordReset\UseCases;

use App\Core\Data\Exceptions\NotFoundException;
use App\Domains\PasswordReset\Notifications\AccountPasswordResetCompleteNotification;
use App\Models\Account;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\DB;

final class ResetAccountPassword
{
    /**
     * @throws NotFoundException if password reset request or account not found
     */
    public function execute(string $token, string $newPassword)
    {
        $passwordReset = PasswordReset::whereToken($token)->firstOrFail();

        $account = Account::whereEmail($passwordReset->email)->firstOrFail();

        DB::transaction(function () use ($account, $passwordReset, $newPassword) {
            $account->updatePassword($newPassword);
            $passwordReset->delete();
        });

        $account->notify(new AccountPasswordResetCompleteNotification());
    }
}
