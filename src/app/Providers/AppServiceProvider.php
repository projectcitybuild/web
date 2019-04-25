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
use Schema;
use App\Entities\Servers\Repositories\ServerCategoryRepositoryCache;
use Illuminate\Contracts\Cache\Factory as Cache;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            \App\Entities\Servers\Repositories\ServerCategoryRepositoryContract::class,
            function($app) {
                return new ServerCategoryRepositoryCache(
                    $app->make(Cache::class),
                    $app->make(\App\Entities\Servers\Repositories\ServerCategoryRepository::class)
                );
            }
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        
        // we don't want to store namespaces
        // in the database so we'll map them
        // to unique keys instead
        Relation::morphMap([
            'minecraft_player' => MinecraftPlayer::class,
            AccountPaymentType::Donation => Donation::class,
            GamePlayerType::Minecraft => MinecraftPlayer::class,
        ]);

        // bind the master view composer to the master view template
        View::composer('front.layouts.master', MasterViewComposer::class);
    }
}
