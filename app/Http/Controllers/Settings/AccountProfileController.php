<?php

namespace App\Http\Controllers\Settings;

use App\Http\WebController;
use Illuminate\Http\Request;

class AccountProfileController extends WebController
{
    public function show(Request $request)
    {
        $account = $request->user();

        return view('v2.front.pages.account.profile')->with(compact('account'));
    }
}
