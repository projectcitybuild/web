<?php

namespace App\Http\Controllers\Front\BanAppeal;

use App\Http\Controllers\WebController;
use Illuminate\Http\Request;

class BanAppealFormController extends WebController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('front.pages.ban-appeal.form');
    }
}
