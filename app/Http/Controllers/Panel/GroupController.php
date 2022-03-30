<?php

namespace App\Http\Controllers\Panel;

use App\Entities\Models\Eloquent\Group;
use App\Http\WebController;

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
