<?php

namespace App\Library\Discourse;

use App\Library\Discourse\Authentication\DiscoursePayloadValidator;
use Illuminate\Support\ServiceProvider;

class DiscourseServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot(): void
    {
    }

    /**
     * Register bindings in the container.
     */
    public function register(): void
    {
        $this->app->bind(DiscoursePayloadValidator::class, function ($app) {
            return new DiscoursePayloadValidator(
                config('discourse.sso_secret')
            );
        });
    }
}
