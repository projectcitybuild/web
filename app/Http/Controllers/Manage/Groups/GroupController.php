<?php

namespace App\Http\Controllers\Manage\Groups;

use App\Http\Controllers\WebController;
use App\Models\Group;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class GroupController extends WebController
{
    public function index()
    {
        Gate::authorize('viewAny', Group::class);

        $groups = Group::withCount('accounts')
            ->orderBy('group_type', 'desc')
            ->orderBy('display_priority', 'asc')
            ->get();

        return view('manage.pages.groups.index')
            ->with(compact('groups'));
    }

    public function create(Request $request)
    {
        Gate::authorize('create', Group::class);

        $group = new Group();

        return view('manage.pages.groups.create')
            ->with(compact('group'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Group::class);

        $request->validate([
            'name' => ['required', 'string', Rule::unique(Group::tableName())],
            'minecraft_name' => ['string', Rule::unique(Group::tableName(), 'minecraft_name')],
            'display_priority' => ['int'],
        ]);

        Group::create($request->all());

        return redirect(route('manage.groups.index'));
    }

    public function edit(Group $group)
    {
        Gate::authorize('update', $group);

        return view('manage.pages.groups.edit')
            ->with(compact('group'));
    }

    public function update(Request $request, Group $group): RedirectResponse
    {
        Gate::authorize('update', $group);

        $request->validate([
            'name' => ['required', 'string', Rule::unique(Group::tableName())->ignore($group)],
            'minecraft_name' => ['string', Rule::unique(Group::tableName(), 'minecraft_name')->ignore($group)],
            'display_priority' => ['nullable', 'int'],
        ]);

        $group->update($request->all());

        return redirect(route('manage.groups.index'));
    }

    public function destroy(Request $request, Group $group): RedirectResponse
    {
        Gate::authorize('delete', $group);

        $group->delete();

        return redirect(route('manage.groups.index'));
    }
}
