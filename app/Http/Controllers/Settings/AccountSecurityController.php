<?php

namespace App\Http\Controllers\Settings;

use App\Http\WebController;
use Illuminate\Http\Request;

class AccountSecurityController extends WebController
{
    public function show()
    {
        return view('front.pages.account.account-security');
    }
}
