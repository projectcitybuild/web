<?php

namespace App\Http\Controllers\Review;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Http\Controllers\WebController;
use Illuminate\Http\Request;

class HomeController extends WebController
{
    use RendersReviewApp;
    use AuthorizesPermissions;

    public function __invoke(Request $request)
    {
        $this->requires('web.review.*');

        return $this->inertiaRender('Home');
    }
}
