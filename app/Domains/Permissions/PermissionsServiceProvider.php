<?php

namespace App\Domains\Permissions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class PermissionsServiceProvider extends ServiceProvider
{
    public function boot(PermissionsRepository $permissionsRepository)
    {
        $this->registerPermissionGate($permissionsRepository);
        $this->registerInertiaProps($permissionsRepository);
    }

    private function registerPermissionGate(PermissionsRepository $permissionsRepository)
    {
        Gate::define(
            ability: 'permission',
            callback: fn ($account, $permissionName) => $permissionsRepository->hasPermission(
                $permissionName,
                $account,
            ),
        );
    }

    private function registerInertiaProps(PermissionsRepository $permissionsRepository)
    {
        Inertia::share([
            'is_admin' => function () {
                $account = Auth::user();
                if (! $account) {
                    return false;
                }
                return $account->isAdmin();
            },
            'permissions' => function () use ($permissionsRepository) {
                $account = Auth::user();
                if (! $account) {
                    return null;
                }
                return $permissionsRepository->getPermissionNames($account);
            },
        ]);
    }
}
