<?php

namespace App\Http\Controllers\Panel;

use App\Entities\Groups\Models\Group;
use App\Http\WebController;
use Illuminate\Http\Request;

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
