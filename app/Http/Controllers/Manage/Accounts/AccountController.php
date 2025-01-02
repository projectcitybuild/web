<?php

namespace App\Http\Controllers\Manage\Accounts;

use App\Core\Rules\DiscourseUsernameRule;
use App\Http\Controllers\WebController;
use App\Http\Filters\LikeFilter;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Pipeline;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class AccountController extends WebController
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Account::class);

        $pipes = [
            new LikeFilter('username', $request->get('username')),
            new LikeFilter('email', $request->get('email')),
        ];
        $accounts = Pipeline::send(Account::query())
            ->through($pipes)
            ->thenReturn()
            ->orderBy('created_at', 'desc')
            ->cursorPaginate(50);

        if ($request->wantsJson()) {
            return $accounts;
        }
        return Inertia::render('Accounts/AccountList', compact('accounts'));
    }

    public function show(Account $account)
    {
        Gate::authorize('view', $account);

        $account->load(['groups', 'badges', 'minecraftAccount', 'emailChangeRequests']);

        return Inertia::render('Accounts/AccountShow', compact('account'));
    }

    public function edit(Account $account)
    {
        Gate::authorize('update', $account);

        return Inertia::render('Accounts/AccountEdit', compact('account'));
    }

    public function update(Request $request, Account $account)
    {
        Gate::authorize('update', $account);

        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                Rule::unique(Account::tableName(), 'email')
                    ->ignore($account),
            ],
            'username' => [
                'required',
                new DiscourseUsernameRule(),
                Rule::unique(Account::tableName(), 'username')
                    ->ignore($account),
            ],
            'password' => [Password::defaults()],
        ]);

        if ($request->has('password')) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $account->update($validated);

        return to_route('manage.accounts.show', $account)
            ->with(['success' => 'Account updated successfully.']);
    }
}
