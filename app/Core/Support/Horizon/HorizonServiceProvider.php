<?php

namespace App\Core\Support\Horizon;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

final class HorizonServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Gate::define('viewHorizon', function ($user) {
            return $user->isAdmin();
        });
    }
}
