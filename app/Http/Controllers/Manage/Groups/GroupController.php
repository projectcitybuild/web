<?php

namespace App\Http\Controllers\Manage\Groups;

use App\Http\Controllers\WebController;
use App\Models\Account;
use App\Models\Group;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class GroupController extends WebController
{
    public function index()
    {
        Gate::authorize('viewAny', Group::class);

        $groups = Group::withCount('accounts')
            ->orderBy('group_type', 'desc')
            ->orderBy('display_priority', 'asc')
            ->get();

        // Default group doesn't have assigned members
        $groups
            ->where('is_default', true)
            ->map(fn ($group) => $group->accounts_count = Account::doesntHave('groups')->count());

        return Inertia::render('Groups/GroupList', compact('groups'));
    }

    public function create(Request $request)
    {
        Gate::authorize('create', Group::class);

        return Inertia::render('Groups/GroupCreate');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Group::class);

        $validated = $request->validate([
            'name' => ['required', 'string', Rule::unique(Group::tableName())],
            'alias' => [],
            'minecraft_name' => [Rule::unique(Group::tableName(), 'minecraft_name')],
            'minecraft_display_name' => [],
            'minecraft_hover_text' => [],
            'group_type' => [],
            'display_priority' => ['int'],
        ]);

        Group::create($validated);

        return to_route('manage.groups.index')
            ->with(['success' => 'Group created successfully.']);
    }

    public function edit(Group $group)
    {
        Gate::authorize('update', $group);

        return Inertia::render('Groups/GroupEdit', compact('group'));
    }

    public function update(Request $request, Group $group): RedirectResponse
    {
        Gate::authorize('update', $group);

        $validated = $request->validate([
            'name' => ['required', 'string', Rule::unique(Group::tableName())->ignore($group)],
            'alias' => [],
            'minecraft_name' => ['string', Rule::unique(Group::tableName(), 'minecraft_name')->ignore($group)],
            'minecraft_display_name' => [],
            'minecraft_hover_text' => [],
            'group_type' => [],
            'display_priority' => ['nullable', 'int'],
        ]);

        $group->update($validated);

        return to_route('manage.groups.index')
            ->with(['success' => 'Group updated successfully.']);
    }

    public function destroy(Request $request, Group $group): RedirectResponse
    {
        Gate::authorize('delete', $group);

        $group->delete();

        return to_route('manage.groups.index')
            ->with(['success' => 'Group deleted successfully.']);
    }
}
