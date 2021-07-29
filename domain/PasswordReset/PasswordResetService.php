<?php

namespace Domain\PasswordReset;

use App\Entities\Accounts\Models\Account;
use App\Entities\Accounts\Models\AccountPasswordReset;
use App\Entities\Accounts\Notifications\AccountPasswordResetCompleteNotification;
use App\Entities\Accounts\Notifications\AccountPasswordResetNotification;
use App\Exceptions\Http\NotFoundException;
use App\Helpers\TokenHelpers;
use App\Http\Actions\AccountSettings\UpdateAccountPassword;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class PasswordResetService
{
    private UpdateAccountPassword $updateAccountPassword;

    public function __construct(UpdateAccountPassword $updateAccountPassword)
    {
        $this->updateAccountPassword = $updateAccountPassword;
    }

    /**
     * Sends a password reset link to the given email address.
     *
     * A AccountPasswordReset is created to tie the email address to
     * a token. The token is required later to perform the actual
     * password request
     */
    public function sendPasswordResetEmail(Account $account, string $email)
    {
        $passwordReset = AccountPasswordReset::updateOrCreate([
            'email' => $email,
        ], [
            'token' => TokenHelpers::generateToken(),
            'created_at' => Carbon::now(),
        ]);

        $account->notify(new AccountPasswordResetNotification($passwordReset));
    }

    /**
     * Resets the password for the account associated with a given
     * AccountPasswordReset token.
     *
     * @throws NotFoundException
     */
    public function resetPassword(string $token, string $newPassword)
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
            $this->updateAccountPassword->execute($account, $newPassword);
            $passwordReset->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $account->notify(new AccountPasswordResetCompleteNotification());
    }

    /**
     * Deletes any password reset requests that are older than 14 days.
     */
    public function deleteExpiredRequests()
    {
        // FIXME: we should be storing this in a AccountPasswordReset column
        //        because this relies on the job queue never breaking...

        Log::info('Running password reset cleanup service...');

        $thresholdDate = now()->subDays(14);
        AccountPasswordReset::whereDate('created_at', '<', $thresholdDate)->delete();
    }
}
