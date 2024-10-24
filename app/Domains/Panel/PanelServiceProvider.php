<?php

namespace App\Domains\Panel;

use App\Domains\Panel\Data\PanelGroupScope;
use App\Models\Account;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

final class PanelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        collect(PanelGroupScope::cases())->each(function ($scope) {
            Gate::define(
                ability: $scope->value,
                callback: fn (Account $account) => $account->hasAbility($scope->value),
            );
        });

        Blade::if('scope', function (PanelGroupScope $scope) {
            return Gate::check($scope->value);
        });
    }
}
