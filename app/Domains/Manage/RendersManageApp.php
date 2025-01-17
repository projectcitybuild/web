<?php

namespace App\Domains\Manage;

use Illuminate\Contracts\Support\Arrayable;
use Inertia\Inertia;
use Inertia\Response;

trait RendersManageApp
{
    public function inertiaRender(string $component, array|Arrayable $props = []): Response
    {
        Inertia::setRootView('manage.app');

        return Inertia::render($component, $props);
    }
}
