<?php

namespace App\Domains\BanAppeals;

use App\Domains\BanAppeals\Policies\BanAppealPolicy;
use App\Models\BanAppeal;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class BanAppealServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Gate::policy(
            class: BanAppeal::class,
            policy: BanAppealPolicy::class,
        );
    }
}
