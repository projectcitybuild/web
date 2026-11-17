<?php

namespace App\Http\Controllers\Admin\Review;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Http\Controllers\Admin\RendersAdminApp;
use App\Http\Controllers\WebController;
use Illuminate\Http\Request;

class HomeController extends WebController
{
    use AuthorizesPermissions;
    use RendersAdminApp;

    public function __invoke(Request $request)
    {
        $this->requires('web.review.*');

        return $this->inertiaRender('Home');
    }
}
