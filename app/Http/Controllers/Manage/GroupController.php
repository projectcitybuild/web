<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\WebController;
use App\Models\Group;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GroupController extends WebController
{
    public function index()
    {
        $groups = Group::withCount('accounts')
            ->orderBy('group_type', 'desc')
            ->orderBy('display_priority', 'asc')
            ->get();

        return view('manage.pages.groups.index')
            ->with(compact('groups'));
    }

    public function create(Request $request)
    {
        $group = new Group();

        return view('manage.pages.groups.create')
            ->with(compact('group'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', Rule::unique(Group::tableName())],
            'minecraft_name' => ['string', Rule::unique(Group::tableName(), 'minecraft_name')],
            'minecraft_display_name' => ['string'],
            'minecraft_hover_text' => ['string'],
            'display_priority' => ['int'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Group::create($request->all());

        return redirect(route('manage.groups.index'));
    }

    public function edit(Group $group)
    {
        return view('manage.pages.groups.edit')
            ->with(compact('group'));
    }

    public function update(Request $request, Group $group): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', Rule::unique(Group::tableName())->ignore($group)],
            'minecraft_name' => ['string', Rule::unique(Group::tableName(), 'minecraft_name')->ignore($group)],
            'minecraft_display_name' => ['string'],
            'minecraft_hover_text' => ['string'],
            'display_priority' => ['nullable', 'int'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $group->update($request->all());

        return redirect(route('manage.groups.index'));
    }

    public function destroy(Request $request, Group $group): RedirectResponse
    {
        $group->delete();

        return redirect(route('manage.groups.index'));
    }
}
