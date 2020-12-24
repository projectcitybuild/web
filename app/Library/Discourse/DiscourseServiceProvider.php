<?php

namespace App\Library\Discourse;

use App\Library\Discourse\Authentication\DiscoursePayloadValidator;
use Illuminate\Support\ServiceProvider;

class DiscourseServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(DiscoursePayloadValidator::class, function ($app) {
            return new DiscoursePayloadValidator(
                config('discourse.sso_secret')
            );
        });
    }
}
