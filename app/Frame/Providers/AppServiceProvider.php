<?php

namespace App\Frame\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Forums\Services\SMF\Smf;
use App\Modules\Forums\Models\ForumUser;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Smf::class, function($app) {
            return new Smf(
                new ForumUser(), 
                config('smf.cookie_name'), 
                config('smf.staff_group_ids')
            );
        });
    }
}
