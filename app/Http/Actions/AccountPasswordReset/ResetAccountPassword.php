<?php

namespace App\Http\Actions\AccountPasswordReset;

use App\Http\Actions\AccountSettings\UpdateAccountPassword;
use App\Entities\Accounts\Models\Account;
use App\Entities\Accounts\Models\AccountPasswordReset;
use App\Entities\Accounts\Notifications\AccountPasswordResetCompleteNotification;
use App\Exceptions\Http\NotFoundException;
use Illuminate\Support\Facades\DB;

final class ResetAccountPassword
{
    /**
     * @var UpdateAccountPassword
     */
    private $updateAccountPassword;

    public function __construct(UpdateAccountPassword $updateAccountPassword)
    {
        $this->updateAccountPassword = $updateAccountPassword;
    }

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

        $account->notify(new AccountPasswordResetCompleteNotification);
    }
}