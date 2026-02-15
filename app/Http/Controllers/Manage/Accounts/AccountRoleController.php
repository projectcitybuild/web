<?php

namespace App\Http\Controllers\Manage\Accounts;

use App\Http\Controllers\Manage\RendersManageApp;
use App\Http\Controllers\WebController;
use App\Models\Account;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AccountRoleController extends WebController
{
    use RendersManageApp;

    public function index(Request $request, Account $account)
    {
        Gate::authorize('viewAny', Account::class);

        $roles = Role::where('is_default', false)->get();
        $accountRoleIds = $account->roles->pluck(Role::primaryKey());

        return $this->inertiaRender('Accounts/AccountRoleSelect', [
            'roles' => $roles,
            'account_role_ids' => $accountRoleIds ?? [],
            'account_id' => $account->getKey(),
        ]);
    }

    public function update(Request $request, Account $account)
    {
        Gate::authorize('update', $account);

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
