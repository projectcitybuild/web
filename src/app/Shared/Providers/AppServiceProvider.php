<?php

namespace App\Shared\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Forums\Services\SMF\{Smf, SmfUserFactory};
use App\Modules\Forums\Repositories\ForumUserRepository;
use App\Modules\Forums\Services\Retrieve\ForumRetrieveInterface;
use App\Modules\Forums\Services\Retrieve\OfflineRetrieve;
use App\Modules\Forums\Services\Retrieve\DatabaseRetrieve;
use App\Modules\Servers\Services\Querying\{ServerQueryService, QueryAdapterFactory};
use App\Modules\Servers\Repositories\{ServerRepository, ServerStatusRepository};
use Schema;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // probably not good to have this...
        Schema::defaultStringLength(191);
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
            $factory = new SmfUserFactory(
                $app->make(ForumUserRepository::class),
                config('smf.staff_group_ids')
            );

            return new Smf(
                $app->make(ForumUserRepository::class),
                config('smf.cookie_name'),
                $factory
            );
        });

        $this->app->bind(ForumRetrieveInterface::class, function($app) {
            if(env('DB_HOST_FORUMS') === null || env('DB_MOCK_FORUMS') === true) {
                return $app->make(OfflineRetrieve::class);
            }
            return $app->make(DatabaseRetrieve::class);
        });

        // bind server query service
        $this->app->bind(ServerQueryService::class, function($app) {
            return new ServerQueryService(
                $app->make(ServerRepository::class),
                $app->make(ServerStatusRepository::class),
                new QueryAdapterFactory(),
                $app->make(\Illuminate\Log\Logger::class)
            );
        });
    }
}
