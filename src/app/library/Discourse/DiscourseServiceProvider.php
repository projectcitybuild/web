<?php
namespace App\Library\Discourse;

use Illuminate\Support\ServiceProvider;
use App\Library\Discourse\Authentication\DiscourseAuthService;

class DiscourseServiceProvider extends ServiceProvider {

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot() {}

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register() {
        $this->app->bind(DiscourseAuthService::class, function($app) {
            return new DiscourseAuthService(
                env('DISCOURSE_SSO_SECRET')
            );
        });
    }

}