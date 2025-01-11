<?php

namespace App\Http\Controllers\Manage\Groups;

use App\Domains\Manage\RendersManageApp;
use App\Http\Controllers\WebController;
use App\Models\Account;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GroupAccountController extends WebController
{
    use RendersManageApp;

    public function index(Request $request, Group $group)
    {
        Gate::authorize('view', $group);

        // Default group doesn't have assigned members
        if ($group->is_default) {
            $accounts = Account::doesntHave('groups');
        } else {
            $accounts = $group->accounts();
        }
        $accounts = $accounts->cursorPaginate(50);

        if ($request->wantsJson()) {
            return $accounts;
        }
        return $this->inertiaRender(
            'Groups/GroupAccountList',
            compact('accounts', 'group'),
        );
    }
}
