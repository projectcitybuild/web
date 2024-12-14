<?php

namespace App\Domains\Manage;

use App\Domains\Manage\Components\ManageSideBarComponent;
use App\Models\Account;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

final class ManageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Gate::define('access-manage', function (Account $account) {
            return $account->isAdmin() || $account->isStaff() || $account->isArchitect();
        });

        Blade::component('panel-side-bar', ManageSideBarComponent::class);
    }
}
