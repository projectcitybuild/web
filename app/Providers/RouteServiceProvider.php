<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        // For backwards compatibility, fallback to v1 if no version specified
        Route::prefix('api')
            ->middleware(['api'])
            ->namespace('App\Http\Controllers\Api\v1')
            ->group(base_path('routes/api_v1.php'));

        Route::prefix('api/v1')
            ->middleware(['api'])
            ->namespace('App\Http\Controllers\Api\v1')
            ->group(base_path('routes/api_v1.php'));

        Route::prefix('api/v2')
            ->middleware(['api'])
            ->namespace('App\Http\Controllers\Api\v2')
            ->group(base_path('routes/api_v2.php'));
    }
}
