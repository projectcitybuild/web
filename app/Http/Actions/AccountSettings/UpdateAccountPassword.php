<?php

namespace App\Http\Actions\AccountSettings;

use Entities\Models\Eloquent\Account;
use Illuminate\Support\Facades\Hash;

/**
 * @final
 */
class UpdateAccountPassword
{
    public function execute(Account $account, string $newPassword)
    {
        if (empty($newPassword)) {
            throw new \Exception('New password cannot be empty');
        }

        $account->password = Hash::make($newPassword);
        $account->save();
    }
}
