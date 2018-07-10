<?php

namespace Front\Controllers;

use Illuminate\Support\Facades\View;

class AccountSocialController extends WebController {

    public function showView() {
        return view('front.pages.account.account-links');
    }

}
