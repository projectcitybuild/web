<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\WebController;
use App\Models\Group;

class GroupAccountController extends WebController
{
    public function index(Group $group)
    {
        $accounts = $group->accounts()->paginate(50);

        return view('admin.account.index')->with([
            'accounts' => $accounts,
            'query' => '',
            'title' => 'Accounts in '.$group->name,
            'showSearch' => false,
        ]);
    }
}
