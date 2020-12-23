<?php

namespace App\Http\Controllers\Panel;

use App\Entities\Accounts\Models\Account;
use App\Http\Actions\SyncUserToDiscourse;
use App\Http\WebController;

class AccountDiscourseForceSync extends WebController
{
    private SyncUserToDiscourse $syncUserToDiscourse;

    public function __construct(SyncUserToDiscourse $syncUserToDiscourse)
    {
        $this->syncUserToDiscourse = $syncUserToDiscourse;
    }

    public function __invoke(Account $account)
    {
        $this->syncUserToDiscourse->setUser($account);
        $this->syncUserToDiscourse->syncAll();

        return redirect()->back();
    }
}
