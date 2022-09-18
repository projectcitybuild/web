<?php

namespace App\Http\Controllers\Front\Account;

use App\Http\Controllers\WebController;
use Illuminate\Http\Request;

class AccountProfileController extends WebController
{
    public function show(Request $request)
    {
        $account = $request->user();

        return view('front.pages.account.profile')->with(compact('account'));
    }
}
