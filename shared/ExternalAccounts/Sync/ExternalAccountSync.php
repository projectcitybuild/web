<?php

namespace Shared\ExternalAccounts\Sync;

use App\Entities\Models\Eloquent\Account;

interface ExternalAccountSync
{
    /**
     * Updates the user's external service account to match
     * the account details (email, etc) of their PCB account
     *
     * @param Account $account PCB account to sync with
     * @return void
     */
    public function sync(Account $account): void;

    /**
     * Updates the username of the given PCB account to match
     * their username on the external service
     *
     * @param Account $account PCB account to update
     * @return void
     */
    public function matchExternalUsername(Account $account): void;
}
