<?php

namespace App\Http\Controllers\Review;

use App\Domains\Review\RendersReviewApp;
use App\Http\Controllers\WebController;
use Illuminate\Http\Request;

class HomeController extends WebController
{
    use RendersReviewApp;

    public function __invoke(Request $request)
    {
        return $this->inertiaRender('Home');
    }
}
