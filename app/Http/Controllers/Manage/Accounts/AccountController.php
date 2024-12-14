<?php

namespace App\Http\Controllers\Manage\Accounts;

use App\Http\Controllers\WebController;
use App\Models\Account;
use App\Models\Badge;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AccountController extends WebController
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Account::class);

        if ($request->has('query') && $request->input('query') !== '') {
            $query = $request->input('query');
            $accounts = Account::search($query)->paginate(50);
        } else {
            $query = '';
            $accounts = Account::paginate(50);
        }

        return view('manage.pages.account.index')
            ->with(compact('accounts', 'query'));
    }

    public function show(Account $account)
    {
        Gate::authorize('view', $account);

        $groups = Group::all();
        $badges = Badge::all();

        return view('manage.pages.account.show')
            ->with(compact('account', 'groups', 'badges'));
    }

    public function edit(Account $account)
    {
        Gate::authorize('update', $account);

        return view('manage.pages.account.edit')
            ->with(compact('account'));
    }

    public function update(
        Request $request,
        Account $account,
    ) {
        Gate::authorize('update', $account);

        // TODO: Validate this
        $account->update($request->all());

        $account->emailChangeRequests()->delete();

        return redirect(route('manage.accounts.show', $account));
    }
}
