<?php

namespace App\Http\Controllers\Front\Account\Settings;

use App\Http\Controllers\WebController;

class MfaStatusController extends WebController
{
    public function show()
    {
        return view('front.pages.account.settings.mfa');
    }
}
