<?php

namespace App\Domains\PasswordReset\UseCases;

use App\Core\Data\Exceptions\NotFoundException;
use App\Domains\PasswordReset\Notifications\AccountPasswordResetCompleteNotification;
use Illuminate\Support\Facades\DB;
use Repositories\AccountPasswordResetRepository;
use Repositories\AccountRepository;

final class ResetAccountPassword
{
    public function __construct(
        private readonly AccountPasswordResetRepository $passwordResetRepository,
        private readonly AccountRepository $accountRepository,
    ) {
    }

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
            $account->updatePassword($newPassword);
            $this->passwordResetRepository->delete($passwordReset);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $account->notify(new AccountPasswordResetCompleteNotification());
    }
}
