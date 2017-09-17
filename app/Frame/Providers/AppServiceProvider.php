<?php

namespace App\Frame\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Forums\Services\SMF\{Smf, SmfUserFactory};
use App\Modules\Forums\Repositories\ForumUserRepository;
use App\Modules\Forums\Models\ForumUser;

use App\Modules\Servers\Services\Querying\{ServerQueryService, QueryAdapterFactory};
use App\Modules\Servers\Repositories\{ServerRepository, ServerStatusRepository};
use App\Modules\Servers\Models\{Server, ServerStatus};


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
        // bind SMF service
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

        // bind server query service
        $this->app->bind(ServerQueryService::class, function($app) {
            return new ServerQueryService(
                new ServerRepository(new Server),
                new ServerStatusRepository(new ServerStatus),
                new QueryAdapterFactory(),
                $app->make('Log')
            );
        });
    }
}
