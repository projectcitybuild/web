<?php

namespace App\Domains\Docs;

use Dedoc\Scramble\Scramble;
use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

final class DocsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Scramble::registerExtension(OpenApiErrorResponseExtension::class);

        Scramble::registerApi('v2', ['info' => ['version' => '2.0']])
            ->routes(function (Route $route) {
                return Str::startsWith($route->uri, 'api/v2');
            });

        Scramble::registerApi('v3', ['info' => ['version' => '3.0']])
            ->routes(function (Route $route) {
                return Str::startsWith($route->uri, 'v3');
            });
    }
}
