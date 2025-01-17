<?php

namespace App\Http\Controllers\Manage;

use App\Domains\Manage\RendersManageApp;
use App\Http\Controllers\WebController;
use Illuminate\Http\Request;

class HomeController extends WebController
{
    use RendersManageApp;

    public function __invoke(Request $request)
    {
        return $this->inertiaRender('Home');
    }
}
