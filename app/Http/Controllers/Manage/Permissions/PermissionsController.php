<?php

namespace App\Http\Controllers\Manage\Permissions;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\Manage\RendersManageApp;
use App\Http\Controllers\WebController;
use App\Models\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PermissionsController extends WebController
{
    use AuthorizesPermissions;
    use RendersManageApp;

    public function index()
    {
        $this->requires(WebManagePermission::PERMISSIONS_VIEW);

        $permissions = Permission::orderBy('name', 'asc')->get();

        return $this->inertiaRender('Permissions/PermissionList', [
            'permissions' => $permissions,
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

        return $this->inertiaRender('Permissions/PermissionEdit', compact('permission'));
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
