<?php

namespace App\Http\Actions\AccountSettings;

use App\Entities\Accounts\Models\Account;

final class UpdateAccountPassword 
{
    public function execute(Account $account, string $newPassword)
    {
        if (empty($newPassword)) {
            throw new Exception('New password cannot be empty');
        }

        $account->password = Hash::make($newPassword);
        $account->save();
    }
}