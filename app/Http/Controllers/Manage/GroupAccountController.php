<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\WebController;
use App\Models\Group;

class GroupAccountController extends WebController
{
    public function index(Group $group)
    {
        $accounts = $group->accounts()->paginate(50);

        return view('manage.account.index')->with([
            'accounts' => $accounts,
            'query' => '',
            'title' => 'Accounts in '.$group->name,
            'showSearch' => false,
        ]);
    }
}
