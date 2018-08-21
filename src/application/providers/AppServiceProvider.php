<?php

namespace Application\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Domains\Modules\Players\Models\MinecraftPlayer;
use Illuminate\Support\Facades\View;
use Schema;
use Domains\Modules\GamePlayerType;

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
        
        // we don't want to store namespaces
        // in the database so we'll map them
        // to unique keys instead
        Relation::morphMap([
            GamePlayerType::Minecraft => MinecraftPlayer::class,
        ]);

        // bind the master view composer to the master view template
        View::composer(
            'front.layouts.master',
            'Interfaces\Web\Composers\MasterViewComposer'
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
