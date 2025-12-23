<?php

namespace App\Domains\Docs;

use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Foundation\Auth\User;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

final class DocsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(SecurityScheme::http('bearer'));
            });

        Scramble::registerApi('v2', ['info' => ['version' => '2.0']])
            ->routes(function (Route $route) {
                return Str::startsWith($route->uri, 'api/v2');
            });

        Scramble::registerApi('v3', ['info' => ['version' => '3.0']])
            ->routes(function (Route $route) {
                return Str::startsWith($route->uri, 'api/v3');
            });

        Gate::define('viewApiDocs', function (User $user) {
            return true;
        });
    }
}
