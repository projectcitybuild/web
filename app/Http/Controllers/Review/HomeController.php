<?php

namespace App\Http\Controllers\Review;

use App\Http\Controllers\WebController;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends WebController
{
    public function __invoke(Request $request)
    {
        return Inertia::render('Home');
    }
}
