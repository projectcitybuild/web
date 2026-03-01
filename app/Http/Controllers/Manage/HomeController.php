<?php

namespace App\Http\Controllers\Manage;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Http\Controllers\WebController;
use Illuminate\Http\Request;

class HomeController extends WebController
{
    use AuthorizesPermissions;
    use RendersManageApp;

    public function __invoke(Request $request)
    {
        $this->requires('web.manage.*');

        return $this->inertiaRender('Home');
    }
}
