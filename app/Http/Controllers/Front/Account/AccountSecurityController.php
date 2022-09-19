<?php

namespace App\Http\Controllers\Front\Account;

use App\Http\Controllers\WebController;

class AccountSecurityController extends WebController
{
    public function show()
    {
        return view('front.pages.account.account-security');
    }
}
