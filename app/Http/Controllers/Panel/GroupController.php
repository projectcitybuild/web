<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\WebController;
use App\Models\Group;

class GroupController extends WebController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups = Group::withCount('accounts')->get();

        return view('admin.group.index')->with(compact('groups'));
    }
}
