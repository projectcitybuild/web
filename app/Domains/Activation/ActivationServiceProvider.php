<?php

namespace App\Domains\Activation;

use App\Domains\Activation\UseCases\SendActivationEmail;
use App\Domains\Registration\Events\AccountCreated;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;

class ActivationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Event::listen(function (AccountCreated $event) {
            App::make(SendActivationEmail::class)
                ->execute($event->account);
        });
    }
}
