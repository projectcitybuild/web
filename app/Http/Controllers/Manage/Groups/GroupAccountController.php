<?php

namespace App\Http\Controllers\Manage\Groups;

use App\Http\Controllers\WebController;
use App\Models\Account;
use App\Models\Group;
use Illuminate\Support\Facades\Gate;

class GroupAccountController extends WebController
{
    public function index(Group $group)
    {
        Gate::authorize('view', $group);

        // Default group doesn't have assigned members
        if ($group->is_default) {
            $accounts = Account::doesntHave('groups');
        } else {
            $accounts = $group->accounts();
        }
        return $accounts->cursorPaginate(50);
    }
}
