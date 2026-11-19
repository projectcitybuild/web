<?php

namespace App\Http\Controllers\Admin\Manage\Permissions;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\Admin\RendersAdminApp;
use App\Http\Controllers\Admin\SortsAdminLists;
use App\Http\Controllers\WebController;
use App\Http\Data\AdminPermissionData;
use App\Models\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PermissionsController extends WebController
{
    use AuthorizesPermissions;
    use RendersAdminApp;
    use SortsAdminLists;

    public function index(Request $request)
    {
        $this->requires(WebManagePermission::PERMISSIONS_VIEW);

        $sortableColumns = [
            'id' => 'id',
            'name' => 'name',
        ];
        $sortState = $this->adminListSortState($request, $sortableColumns);

        $query = Permission::query();
        $this->applyAdminListSort($query, $sortableColumns, $sortState, 'name', 'asc');
        $permissions = $query->get()
            ->map(fn (Permission $permission): array => AdminPermissionData::fromModel($permission)->toArray());

        return $this->inertiaRender('Permissions/PermissionList', [
            'permissions' => $permissions,
            ...$sortState,
        ]);
    }

    public function create(Request $request)
    {
        $this->requires(WebManagePermission::PERMISSIONS_EDIT);

        return $this->inertiaRender('Permissions/PermissionCreate');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->requires(WebManagePermission::PERMISSIONS_EDIT);

        $validated = $request->validate([
            'name' => ['required', 'string', Rule::unique(Permission::tableName())],
        ]);

        Permission::create($validated);

        return to_route('manage.permissions.index')
            ->with(['success' => 'Permission created successfully.']);
    }

    public function edit(Permission $permission)
    {
        $this->requires(WebManagePermission::PERMISSIONS_EDIT);

        return $this->inertiaRender('Permissions/PermissionEdit', [
            'permission' => AdminPermissionData::fromModel($permission),
        ]);
    }

    public function update(Request $request, Permission $permission): RedirectResponse
    {
        $this->requires(WebManagePermission::PERMISSIONS_EDIT);

        $validated = $request->validate([
            'name' => ['required', 'string', Rule::unique(Permission::tableName())->ignore($permission)],
        ]);

        $permission->update($validated);

        return to_route('manage.permissions.index')
            ->with(['success' => 'Permission updated successfully.']);
    }

    public function destroy(Request $request, Permission $permission): RedirectResponse
    {
        $this->requires(WebManagePermission::PERMISSIONS_EDIT);

        $permission->roles()->detach();
        $permission->delete();

        return to_route('manage.permissions.index')
            ->with(['success' => 'Permission deleted successfully.']);
    }
}
