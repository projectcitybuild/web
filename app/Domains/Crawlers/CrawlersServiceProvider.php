<?php

namespace App\Domains\Crawlers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class CrawlersServiceProvider extends ServiceProvider
{
    public function register()
    {
        Blade::directive('noindex', function () {
            return '<meta name="robots" content="noindex, nofollow">';
        });
    }
}
