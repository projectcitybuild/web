<?php

namespace Domain\PasswordReset\UseCases;

use App\Entities\Models\Eloquent\Account;
use App\Entities\Models\Eloquent\AccountPasswordReset;
use App\Entities\Notifications\AccountPasswordResetCompleteNotification;
use App\Exceptions\Http\NotFoundException;
use App\Http\Actions\AccountSettings\UpdateAccountPassword;
use Illuminate\Support\Facades\DB;

final class ResetAccountPassword
{
    public function __construct(
        private UpdateAccountPassword $updateAccountPassword,
    ) {}

    public function execute(string $token, string $newPassword)
    {
        $passwordReset = AccountPasswordReset::where('token', $token)->first();
        if ($passwordReset === null) {
            throw new NotFoundException('no_password_reset', 'Password reset request not found');
        }

        $account = Account::where('email', $passwordReset->email)->first();
        if ($account === null) {
            throw new NotFoundException('no_account', 'Account not found');
        }

        DB::beginTransaction();
        try {
            $this->updateAccountPassword->execute(
                $account,
                $newPassword
            );

            $passwordReset->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $account->notify(new AccountPasswordResetCompleteNotification());
    }
}
