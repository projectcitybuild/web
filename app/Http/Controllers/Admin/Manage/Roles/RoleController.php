<?php

namespace App\Http\Controllers\Admin\Manage\Roles;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\Admin\RendersAdminApp;
use App\Http\Controllers\Admin\SortsAdminLists;
use App\Http\Controllers\WebController;
use App\Http\Data\AdminRoleData;
use App\Models\Account;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class RoleController extends WebController
{
    use AuthorizesPermissions;
    use RendersAdminApp;
    use SortsAdminLists;

    public function index(Request $request)
    {
        $this->requires(WebManagePermission::ROLES_VIEW);

        $sortableColumns = [
            'id' => 'id',
            'name' => 'name',
            'minecraft_name' => 'minecraft_name',
            'role_type' => 'role_type',
            'display_priority' => 'display_priority',
            'is_default' => 'is_default',
            'is_admin' => 'is_admin',
            'additional_homes' => 'additional_homes',
        ];
        $sortState = $this->adminListSortState($request, $sortableColumns);

        $roles = function () use ($sortableColumns, $sortState) {
            $query = Role::withCount('accounts')->withCount('permissions');

            if ($sortState['sort'] === null) {
                $query->orderBy('role_type', 'desc')
                    ->orderBy('display_priority', 'asc')
                    ->orderBy('id', 'desc');
            } else {
                $this->applyAdminListSort($query, $sortableColumns, $sortState, 'role_type');
            }

            $roles = $query->get();

            // Default role doesn't have assigned members
            $roles
                ->where('is_default', true)
                ->map(fn ($role) => $role->accounts_count = Account::doesntHave('roles')->count());

            return $roles;
        };

        return $this->inertiaRender('Roles/RoleList', [
            'roles' => Inertia::defer(fn () => $roles()->map(
                fn (Role $role): array => AdminRoleData::fromModel($role)->toArray(),
            )),
            ...$sortState,
        ]);
    }

    public function create(Request $request)
    {
        $this->requires(WebManagePermission::ROLES_EDIT);

        return $this->inertiaRender('Roles/RoleCreate');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->requires(WebManagePermission::ROLES_EDIT);

        $validated = $request->validate([
            'name' => ['required', 'string', Rule::unique(Role::tableName())],
            'alias' => [],
            'minecraft_name' => [Rule::unique(Role::tableName(), 'minecraft_name')],
            'minecraft_display_name' => [],
            'minecraft_hover_text' => [],
            'additional_homes' => ['required', 'int', 'gte:0'],
            'role_type' => [],
            'display_priority' => 'int',
        ]);

        if ($validated['additional_homes'] === 0) {
            $validated['additional_homes'] = null;
        }

        Role::create($validated);

        return to_route('manage.roles.index')
            ->with(['success' => 'Role created successfully.']);
    }

    public function edit(Role $role)
    {
        $this->requires(WebManagePermission::ROLES_EDIT);

        return $this->inertiaRender('Roles/RoleEdit', [
            'role' => AdminRoleData::fromModel($role),
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $this->requires(WebManagePermission::ROLES_EDIT);

        $validated = $request->validate([
            'name' => ['required', 'string', Rule::unique(Role::tableName())->ignore($role)],
            'alias' => [],
            'minecraft_name' => ['string', Rule::unique(Role::tableName(), 'minecraft_name')->ignore($role)],
            'minecraft_display_name' => [],
            'minecraft_hover_text' => [],
            'additional_homes' => ['required', 'int', 'gte:0'],
            'role_type' => [],
            'display_priority' => ['nullable', 'int'],
        ]);

        if ($validated['additional_homes'] === 0) {
            $validated['additional_homes'] = null;
        }

        $role->update($validated);

        return to_route('manage.roles.index')
            ->with(['success' => 'Role updated successfully.']);
    }

    public function destroy(Request $request, Role $role): RedirectResponse
    {
        $this->requires(WebManagePermission::ROLES_EDIT);

        $role->delete();

        return to_route('manage.roles.index')
            ->with(['success' => 'Role deleted successfully.']);
    }
}
