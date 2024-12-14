<?php

namespace App\Http\Controllers\Manage\Groups;

use App\Http\Controllers\WebController;
use App\Models\Group;
use Illuminate\Support\Facades\Gate;

class GroupAccountController extends WebController
{
    public function index(Group $group)
    {
        Gate::authorize('viewAny', Group::class);

        $accounts = $group->accounts()->paginate(50);

        return view('manage.pages.account.index')->with([
            'accounts' => $accounts,
            'query' => '',
            'title' => 'Accounts in '.$group->name,
            'showSearch' => false,
        ]);
    }
}
