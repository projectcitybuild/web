<?php

namespace App\Http\Controllers\Settings;

use App\Http\WebController;

class AccountSecurityController extends WebController
{
    public function show()
    {
        return view('v2.front.pages.account.account-security');
    }
}
