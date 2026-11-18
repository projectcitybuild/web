<?php

namespace App\Http\Controllers\Admin\Manage\Roles;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\Admin\RendersAdminApp;
use App\Http\Controllers\WebController;
use App\Http\Data\AdminAccountData;
use App\Http\Data\AdminRoleData;
use App\Models\Account;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleAccountController extends WebController
{
    use AuthorizesPermissions;
    use RendersAdminApp;

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
        $accounts->through(fn (Account $account): array => AdminAccountData::fromModel($account, canViewEmail: true)->toArray());

        return $this->inertiaRender(
            'Roles/RoleAccountList',
            [
                'accounts' => $accounts,
                'role' => AdminRoleData::fromModel($role),
            ],
        );
    }
}
