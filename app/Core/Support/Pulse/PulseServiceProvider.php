<?php

namespace App\Core\Support\Pulse;

use App\Models\Account;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

final class PulseServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Gate::define('viewPulse', function (Account $account) {
            return $account->isAdmin();
        });
    }
}
