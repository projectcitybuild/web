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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        //
    }
}
