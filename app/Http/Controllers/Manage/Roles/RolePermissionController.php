<?php

namespace App\Http\Controllers\Manage\Roles;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\Manage\RendersManageApp;
use App\Http\Controllers\WebController;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RolePermissionController extends WebController
{
    use AuthorizesPermissions;
    use RendersManageApp;

    public function index(Request $request, Role $role)
    {
        $this->requires(WebManagePermission::PERMISSIONS_ASSIGN);

        $permissions = Permission::orderBy('name', 'asc')->get();
        $rolePermissionIds = $role->permissions->pluck(Permission::primaryKey());

        return $this->inertiaRender('Roles/RolePermissionSelect', [
            'permissions' => $permissions,
            'role_permission_ids' => $rolePermissionIds ?? [],
            'role_id' => $role->id,
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $this->requires(WebManagePermission::PERMISSIONS_ASSIGN);

        $validated = collect($request->validate([
            'role_permission_ids' => ['array'],
        ]));

        $role->permissions()->sync(
            $validated->get('role_permission_ids', []),
        );

        return to_route('manage.roles.index')
            ->with(['success' => 'Role permissions updated successfully.']);
    }
}
