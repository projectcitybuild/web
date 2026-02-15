<?php

namespace App\Http\Controllers\Manage\Roles;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\Manage\RendersManageApp;
use App\Http\Controllers\WebController;
use App\Models\Account;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleAccountController extends WebController
{
    use AuthorizesPermissions;
    use RendersManageApp;

    public function index(Request $request, Role $role)
    {
        $this->requires(WebManagePermission::ROLES_VIEW);

        // Default role doesn't have assigned members
        if ($role->is_default) {
            $accounts = Account::doesntHave('roles');
        } else {
            $accounts = $role->accounts();
        }
        $accounts = $accounts->paginate(50);

        if ($request->wantsJson()) {
            return $accounts;
        }
        return $this->inertiaRender(
            'Roles/RoleAccountList',
            compact('accounts', 'role'),
        );
    }
}
