<?php

namespace App\Http\Controllers\Panel;

use App\Entities\Models\Eloquent\Account;
use App\Http\WebController;
use Shared\ExternalAccounts\Sync\ExternalAccountSync;

class AccountDiscourseForceSync extends WebController
{
    public function __invoke(Account $account, ExternalAccountSync $externalAccountSync)
    {
        $externalAccountSync->sync($account);

        return redirect()->back();
    }
}
