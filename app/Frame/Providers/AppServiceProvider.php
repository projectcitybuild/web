<?php

namespace App\Frame\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Forums\Services\SMF\{Smf, SmfUserFactory};
use App\Modules\Forums\Repositories\ForumUserRepository;
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
            $repository = new ForumUserRepository(new ForumUser);

            $factory = new SmfUserFactory(
                $repository, 
                config('smf.staff_group_ids')
            );

            return new Smf(
                $repository, 
                config('smf.cookie_name'),
                $factory
            );
        });
    }
}
