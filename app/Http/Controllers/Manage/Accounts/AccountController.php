<?php

namespace App\Http\Controllers\Manage\Accounts;

use App\Core\Rules\DiscourseUsernameRule;
use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Domains\Registration\UseCases\CreateUnactivatedAccount;
use App\Http\Controllers\Manage\RendersManageApp;
use App\Http\Controllers\WebController;
use App\Http\Filters\EqualFilter;
use App\Http\Filters\LikeFilter;
use App\Models\Account;
use App\Http\Resources\AccountResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Pipeline;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class AccountController extends WebController
{
    use AuthorizesPermissions;
    use RendersManageApp;

    public function index(Request $request)
    {
        $this->requires(WebManagePermission::ACCOUNTS_VIEW);

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

        // Using a Resource::collection transforms the shape of the paginated
        // data. To preserve the frontend, we need to manually map it back to
        // the non-resource shape
        $transform = function () use ($accounts) {
            $paginator = $accounts();
            return [
                'data' => AccountResource::collection($paginator->items()),
                'current_page' => $paginator->currentPage(),
                'total' => $paginator->total(),
                'path' => $paginator->path(),
                'next_page_url' => $paginator->nextPageUrl(),
            ];
        };

        if ($request->wantsJson()) {
            return $transform();
        }
        return $this->inertiaRender('Accounts/AccountList', [
            'accounts' => Inertia::defer($transform),
        ]);
    }

    public function show(Account $account)
    {
        $this->requires(WebManagePermission::ACCOUNTS_VIEW);

        $account->load([
            'roles',
            'badges',
            'minecraftAccount',
            'activations',
            'donations',
        ]);

        if ($this->can(WebManagePermission::ACCOUNTS_VIEW_EMAIL)) {
            $account->load('emailChangeRequests');
        }

        return $this->inertiaRender('Accounts/AccountShow', [
            'account' => new AccountResource($account),
        ]);
    }

    public function create()
    {
        $this->requires(WebManagePermission::ACCOUNTS_EDIT);

        return $this->inertiaRender('Accounts/AccountCreate');
    }

    public function store(Request $request, CreateUnactivatedAccount $createUnactivatedAccount)
    {
        $this->requires(WebManagePermission::ACCOUNTS_EDIT);

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
        $this->requires(WebManagePermission::ACCOUNTS_EDIT);

        return $this->inertiaRender('Accounts/AccountEdit', compact('account'));
    }

    public function update(Request $request, Account $account)
    {
        $this->requires(WebManagePermission::ACCOUNTS_EDIT);

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
