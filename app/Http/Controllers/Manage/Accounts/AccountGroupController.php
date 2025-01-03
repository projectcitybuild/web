<?php

namespace App\Http\Controllers\Manage\Accounts;

use App\Http\Controllers\WebController;
use App\Models\Account;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class AccountGroupController extends WebController
{
    public function index(Request $request, Account $account)
    {
        Gate::authorize('viewAny', Account::class);

        $groups = Group::where('is_default', false)->get();
        $accountGroupIds = $account->groups->pluck(Group::primaryKey());

        return Inertia::render('Accounts/AccountGroupSelect', [
            'groups' => $groups,
            'account_group_ids' => $accountGroupIds ?? [],
            'account_id' => $account->getKey(),
        ]);
    }

    public function update(Request $request, Account $account)
    {
        Gate::authorize('update', $account);

        $request->validate([
            'account_group_ids' => [],
        ]);

        $account->groups()->sync(
            $request->get('account_group_ids', []),
        );

        return to_route('manage.accounts.show', $account)
            ->with(['success' => 'Ggoups updated successfully.']);
    }
}
