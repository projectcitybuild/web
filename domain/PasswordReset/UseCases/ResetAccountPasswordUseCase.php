<?php

namespace Domain\PasswordReset\UseCases;

use App\Entities\Models\Eloquent\Account;
use App\Entities\Models\Eloquent\AccountPasswordReset;
use App\Entities\Notifications\AccountPasswordResetCompleteNotification;
use App\Entities\Repositories\AccountRepository;
use App\Exceptions\Http\NotFoundException;
use App\Http\Actions\AccountSettings\UpdateAccountPassword;
use Domain\PasswordReset\Repositories\AccountPasswordResetRepository;
use Illuminate\Support\Facades\DB;

final class ResetAccountPasswordUseCase
{
    public function __construct(
        private UpdateAccountPassword $updateAccountPassword,
        private AccountPasswordResetRepository $passwordResetRepository,
        private AccountRepository $accountRepository,
    ) {}

    /**
     * @throws NotFoundException if password reset request or account not found
     */
    public function execute(string $token, string $newPassword)
    {
        $passwordReset = $this->passwordResetRepository->firstByToken($token)
            ?? throw new NotFoundException('no_password_reset', 'Password reset request not found');

        $account = $this->accountRepository->getByEmail($passwordReset->email)
            ?? throw new NotFoundException('no_account', 'Account not found');

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
