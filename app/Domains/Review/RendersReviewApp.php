<?php

namespace App\Domains\Review;

use Illuminate\Contracts\Support\Arrayable;
use Inertia\Inertia;
use Inertia\Response;

trait RendersReviewApp
{
    public function inertiaRender(string $component, array|Arrayable $props = []): Response
    {
        Inertia::setRootView('review.app');

        return Inertia::render($component, $props);
    }
}
