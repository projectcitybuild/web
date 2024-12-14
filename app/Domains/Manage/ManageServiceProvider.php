<?php

namespace App\Domains\Manage;

use App\Domains\Manage\Components\ManageSideBarComponent;
use App\Domains\Manage\Data\PanelGroupScope;
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
        collect(PanelGroupScope::cases())->each(function ($scope) {
            Gate::define(
                ability: $scope->value,
                callback: fn (Account $account) => $account->hasAbility($scope->value),
            );
        });

        Blade::if('scope', function (PanelGroupScope $scope) {
            return Gate::check($scope->value);
        });

        Blade::component('panel-side-bar', ManageSideBarComponent::class);
    }
}
