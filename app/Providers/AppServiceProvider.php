<?php

namespace App\Providers;

use App\Entities\Donations\Models\Donation;
use App\Entities\GamePlayerType;
use App\Entities\Payments\AccountPaymentType;
use App\Entities\Players\Models\MinecraftPlayer;
use App\Entities\Servers\Repositories\ServerCategoryRepository;
use App\Entities\Servers\Repositories\ServerCategoryRepositoryContract;
use App\Entities\Servers\Repositories\ServerStatusPlayerRepository;
use App\Http\Composers\MasterViewComposer;
use App\Services\Queries\ServerQueryService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
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
        $this->app->bind(ServerCategoryRepositoryContract::class, function ($app) {
            return new ServerCategoryRepository();
        });
        $this->app->singleton(ServerQueryService::class, function ($app) {
            return new ServerQueryService(
                $app->make(ServerStatusPlayerRepository::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        // We don't want to store namespaces in the database so we'll map them
        // to unique keys instead
        Relation::morphMap([
            AccountPaymentType::Donation => Donation::class,
            GamePlayerType::Minecraft => MinecraftPlayer::class,
        ]);

        // Bind the master view composer to the master view template
        View::composer('front.layouts.master', MasterViewComposer::class);

        // Fix the factory() function always searching for factory files with a relative namespace
        Factory::guessFactoryNamesUsing(function (string $modelName) {
            return 'Database\Factories\\'.class_basename($modelName).'Factory';
        });
    }
}
