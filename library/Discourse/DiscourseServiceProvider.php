<?php

namespace Library\Discourse;

use Illuminate\Support\ServiceProvider;
use Library\Discourse\Authentication\DiscoursePayloadValidator;
use function config;

class DiscourseServiceProvider extends ServiceProvider
{
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
