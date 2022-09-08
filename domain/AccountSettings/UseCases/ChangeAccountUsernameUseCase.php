<?php

namespace Domain\Accounts\UseCases;

use Entities\Models\Eloquent\Account;

/**
 * @final
 */
class ChangeAccountUsernameUseCase
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
