<?php

namespace App\Domains\Permissions;

use Illuminate\Support\Facades\Gate;

trait AuthorizesPermissions
{
    private const GATE_NAME = 'permission';

    public function requires(string|WebManagePermission $permissionName)
    {
        $permissionName = $permissionName instanceof WebManagePermission
            ? $permissionName->value
            : $permissionName;

        if (! Gate::allows(self::GATE_NAME, $permissionName)) {
            abort(403);
        }
    }

    public function can(string|WebManagePermission $permissionName)
    {
        $permissionName = $permissionName instanceof WebManagePermission
            ? $permissionName->value
            : $permissionName;

        return Gate::allows(self::GATE_NAME, $permissionName);
    }
}
