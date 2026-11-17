<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Contracts\Support\Arrayable;
use Inertia\Inertia;
use Inertia\Response;

trait RendersAdminApp
{
    public function inertiaRender(string $component, array|Arrayable $props = []): Response
    {
        Inertia::setRootView('admin.app');

        return Inertia::render($component, $props);
    }
}
