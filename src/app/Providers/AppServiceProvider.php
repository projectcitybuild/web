<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Entities\Players\Models\MinecraftPlayer;
use Illuminate\Support\Facades\View;
use App\Entities\Donations\Models\Donation;
use App\Entities\Payments\AccountPaymentType;
use App\Http\Composers\MasterViewComposer;
use App\Entities\GamePlayerType;
use App\Entities\Servers\Repositories\ServerCategoryRepository;
use App\Entities\Servers\Repositories\ServerCategoryRepositoryCache;
use App\Entities\Servers\Repositories\ServerCategoryRepositoryContract;
use Illuminate\Contracts\Cache\Factory as Cache;
use App\Services\Queries\ServerQueryService;
use App\Entities\Servers\Repositories\ServerStatusPlayerRepository;
use App\Library\Mojang\Api\MojangPlayerApi;
use App\Library\Mojang\Api\MojangPlayerApiContract;
use App\Library\Mojang\Api\MojangPlayerApiThrottled;
use GuzzleHttp\Client;
use Schema;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->bindContractsToInstances();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        
        $this->mapModelNamesToKeys();
        $this->bindViewComposers();
    }

    private function mapModelNamesToKeys()
    {
        // When storing model names in the database, Laravel by default will prefix the
        // namespace to the model name. We don't want implementation details in the database
        // so insteasd we'll map the model names to unique keys that won't change
        Relation::morphMap([
            AccountPaymentType::Donation => Donation::class,
            GamePlayerType::Minecraft => MinecraftPlayer::class,
        ]);
    }

    private function bindViewComposers()
    {
        // Bind the master view composer to the master view template
        View::composer('front.layouts.master', MasterViewComposer::class);
    }

    private function bindContractsToInstances()
    {
        $this->app->bind(ServerCategoryRepositoryContract::class, function($app) {
            return new ServerCategoryRepositoryCache(
                $app->make(Cache::class),
                $app->make(ServerCategoryRepository::class)
            );
        });
        $this->app->singleton(\App\Services\Queries\ServerQueryService::class, function($app) {
            return new ServerQueryService(
                $app->make(ServerStatusPlayerRepository::class)
            );
        });
        $this->app->bind(MojangPlayerApiContract::class, function($app) {
            $client = $app->make(Client::class);
            $api = new MojangPlayerApi($client);
            return new MojangPlayerApiThrottled($api);
        });
    }
}
