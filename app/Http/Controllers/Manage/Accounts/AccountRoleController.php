<?php

namespace App\Http\Controllers\Manage\Accounts;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\Manage\RendersManageApp;
use App\Http\Controllers\WebController;
use App\Models\Account;
use App\Models\Role;
use Illuminate\Http\Request;

class AccountRoleController extends WebController
{
    use AuthorizesPermissions;
    use RendersManageApp;

    public function index(Request $request, Account $account)
    {
        $this->requires(WebManagePermission::ACCOUNTS_VIEW);

        $roles = Role::where('is_default', false)->get();
        $accountRoleIds = $account->roles->pluck(Role::primaryKey());

        return $this->inertiaRender('Accounts/AccountRoleSelect', [
            'roles' => $roles,
            'account_role_ids' => $accountRoleIds ?? [],
            'account_id' => $account->id,
        ]);
    }

    public function update(Request $request, Account $account)
    {
        $this->requires(WebManagePermission::ACCOUNTS_EDIT);

        $validated = collect($request->validate([
            'account_role_ids' => ['array'],
        ]));

        $account->roles()->sync(
            $validated->get('account_role_ids', []),
        );

        return to_route('manage.accounts.show', $account)
            ->with(['success' => 'Roles updated successfully.']);
    }
}
