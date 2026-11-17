<?php

namespace App\Http\Controllers\Manage\Accounts;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\Manage\RendersManageApp;
use App\Http\Controllers\WebController;
use App\Models\Account;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class AccountRoleController extends WebController
{
    use AuthorizesPermissions;
    use RendersManageApp;

    public function index(Request $request, Account $account): Response
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

    public function update(Request $request, Account $account): RedirectResponse
    {
        $this->requires(WebManagePermission::ACCOUNTS_EDIT);

        $validated = collect($request->validate([
            'account_role_ids' => ['array'],
        ]));

        $account->auditSync(
            'roles',
            $validated->get('account_role_ids', []),
            true,
            ['roles.id', 'roles.name'],
        );

        return to_route('manage.accounts.show', $account)
            ->with(['success' => 'Roles updated successfully.']);
    }
}
