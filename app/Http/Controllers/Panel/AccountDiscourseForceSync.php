<?php

namespace App\Http\Controllers\Panel;

use App\Entities\Models\Eloquent\Account;
use App\Http\Actions\SyncUserToDiscourse;
use App\Http\WebController;

class AccountDiscourseForceSync extends WebController
{
    /**
     * @var SyncUserToDiscourse
     */
    private $syncUserToDiscourse;

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
