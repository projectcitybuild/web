<?php

namespace App\Http\Controllers\Manage\Accounts;

use App\Http\Controllers\WebController;
use App\Models\Account;
use Illuminate\Support\Facades\Gate;

class AccountBuilderRankApplicationsController extends WebController
{
    public function index(Account $account)
    {
        Gate::authorize('view', $account);

        return $account
            ->builderRankApplications()
            ->paginate(50);
    }
}
