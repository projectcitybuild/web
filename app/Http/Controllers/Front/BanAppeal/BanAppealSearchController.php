<?php

namespace App\Http\Controllers\Front\BanAppeal;

use App\Http\Controllers\WebController;
use Illuminate\Http\Request;

class BanAppealSearchController extends WebController
{
    public function index(Request $request)
    {
        return view('front.pages.ban-appeal.search');
    }
}
