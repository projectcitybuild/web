<?php

namespace App\Http\Actions\AccountSettings;

use Entities\Models\Eloquent\Account;

final class UpdateAccountUsername
{
    public function execute(Account $account, string $newUsername)
    {
        if (empty($newUsername)) {
            throw new \Exception('New email address cannot be empty');
        }

        $account->username = $newUsername;
        $account->save();
    }
}
