<?php

namespace App\Core\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Forums\Services\Retrieve\OfflineRetrieve;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Modules\Players\Models\MinecraftPlayer;
use Illuminate\Support\Facades\View;
use Schema;
use App\Modules\Discourse\Services\Authentication\DiscourseAuthService;
use Illuminate\Support\Facades\Auth;

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
        
        
        // we don't want implementation details in our database,
        // so convert model namespaces to a unique key instead
        Relation::morphMap([
            'minecraft_player' => MinecraftPlayer::class,
        ]);

        // bind the master view composer to the master view template
        View::composer(
            'layouts.master', 'Front\Composers\MasterViewComposer'
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(DiscourseAuthService::class, function($app) {
            return new DiscourseAuthService(
                env('DISCOURSE_SSO_SECRET')
            );
        });
    }
}
