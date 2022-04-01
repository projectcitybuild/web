<?php

namespace App\Http\Controllers\Panel;

use App\Entities\Models\Eloquent\Group;
use App\Http\WebController;

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
