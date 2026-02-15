<?php

namespace App\Http\Controllers\Manage\Roles;

use App\Http\Controllers\Manage\RendersManageApp;
use App\Http\Controllers\WebController;
use App\Models\Account;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RoleAccountController extends WebController
{
    use RendersManageApp;

    public function index(Request $request, Role $role)
    {
        Gate::authorize('view', $role);

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
