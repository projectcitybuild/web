<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\WebController;
use Inertia\Inertia;

class HomeController extends WebController
{
    public function __invoke()
    {
        return Inertia::render('home', []);
    }
}
