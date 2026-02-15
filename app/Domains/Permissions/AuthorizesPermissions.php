<?php

namespace App\Domains\Permissions;

use Illuminate\Support\Facades\Gate;

trait AuthorizesPermissions
{
    public function requires(string|WebManagePermission $permissionName)
    {
        $permissionName = $permissionName instanceof WebManagePermission
            ? $permissionName->value
            : $permissionName;

        if (! Gate::allows('permission', $permissionName)) {
            abort(403);
        }
    }
}
