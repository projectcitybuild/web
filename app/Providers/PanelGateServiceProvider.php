<?php

namespace App\Providers;

use Entities\Models\Eloquent\Account;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

final class PanelGateServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::define('panel-manage-accounts', function (Account $account) {
            return $account->hasAbility('panel-manage-accounts');
        });
    }
}
