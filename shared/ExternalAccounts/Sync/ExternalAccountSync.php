<?php

namespace Shared\ExternalAccounts\Sync;

use App\Entities\Models\Eloquent\Account;

interface ExternalAccountSync
{
    public function sync(Account $account): void;
}
