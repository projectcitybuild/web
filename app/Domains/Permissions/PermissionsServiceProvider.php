<?php

namespace App\Domains\Permissions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class PermissionsServiceProvider extends ServiceProvider
{
    public function __construct(
        private readonly PermissionsRepository $permissionsRepository,
    ) {}

    public function boot()
    {
        $this->registerPermissionGate();
        $this->registerInertiaProps();
    }

    private function registerPermissionGate()
    {
        Gate::define(
            ability: 'permission',
            callback: fn ($account, $permissionName) => $this->permissionsRepository->hasPermission(
                $permissionName,
                $account,
            ),
        );
    }

    private function registerInertiaProps()
    {
        Inertia::share([
            'permissions' => function () {
                $account = Auth::user();
                if (!$account) return null;
                return $this->permissionsRepository->getPermissionNames($account);
            },
        ]);
    }
}
