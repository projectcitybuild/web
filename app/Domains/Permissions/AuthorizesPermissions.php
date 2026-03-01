<?php

namespace App\Domains\Permissions;

use Illuminate\Support\Facades\Gate;

trait AuthorizesPermissions
{
    private const GATE_NAME = 'permission';

    public function requires(string|WebManagePermission|WebReviewPermission $permission)
    {
        if (! $this->can($permission)) {
            abort(403);
        }
    }

    public function can(string|WebManagePermission|WebReviewPermission $permission)
    {
        $node = ($permission instanceof WebManagePermission || $permission instanceof WebReviewPermission)
            ? $permission->value
            : $permission;

        return Gate::allows(self::GATE_NAME, $node);
    }
}
