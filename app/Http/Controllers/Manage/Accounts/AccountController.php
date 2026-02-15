<?php

namespace App\Http\Controllers\Manage\Accounts;

use App\Core\Rules\DiscourseUsernameRule;
use App\Domains\Registration\UseCases\CreateUnactivatedAccount;
use App\Http\Controllers\Manage\RendersManageApp;
use App\Http\Controllers\WebController;
use App\Http\Filters\EqualFilter;
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
    use RendersManageApp;

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Account::class);

        $accounts = function () use ($request) {
            $pipes = [
                new LikeFilter('username', $request->query->get('username')),
                new LikeFilter('email', $request->query->get('email')),
                new EqualFilter('activated', $request->query->get('activated')),
            ];
            return Pipeline::send(Account::query())
                ->through($pipes)
                ->thenReturn()
                ->with('minecraftAccount')
                ->orderBy('created_at', 'desc')
                ->paginate(50);
        };

        if ($request->wantsJson()) {
            return $accounts();
        }
        return $this->inertiaRender('Accounts/AccountList', [
            'accounts' => Inertia::defer($accounts),
        ]);
    }

    public function show(Account $account)
    {
        Gate::authorize('view', $account);

        $account->load([
            'roles',
            'badges',
            'minecraftAccount',
            'emailChangeRequests',
            'activations',
            'donations',
        ]);

        return $this->inertiaRender('Accounts/AccountShow', compact('account'));
    }

    public function create()
    {
        Gate::authorize('create', Account::class);

        return $this->inertiaRender('Accounts/AccountCreate');
    }

    public function store(Request $request, CreateUnactivatedAccount $createUnactivatedAccount)
    {
        Gate::authorize('create', Account::class);

        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                Rule::unique(Account::tableName(), 'email'),
            ],
            'username' => [
                'required',
                new DiscourseUsernameRule,
                Rule::unique(Account::tableName(), 'username'),
            ],
            'password' => [Password::defaults()],
        ]);

        $account = $createUnactivatedAccount->execute(
            email: $validated['email'],
            username: $validated['username'],
            password: $validated['password'],
            sendActivationEmail: false,
        );

        return to_route('manage.accounts.show', $account)
            ->with(['success' => 'Account created successfully.']);
    }

    public function edit(Account $account)
    {
        Gate::authorize('update', $account);

        return $this->inertiaRender('Accounts/AccountEdit', compact('account'));
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
                new DiscourseUsernameRule,
                Rule::unique(Account::tableName(), 'username')
                    ->ignore($account),
            ],
            'password' => ['nullable', Password::defaults()],
        ]);

        if ($request->has('password')) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $account->update($validated);

        return to_route('manage.accounts.show', $account)
            ->with(['success' => 'Account updated successfully.']);
    }
}
