<?php

namespace App\Http\Controllers\Settings;

use App\Http\WebController;
use Illuminate\Http\Request;

final class AccountBillingController extends WebController
{
    public function index(Request $request)
    {
        return $request->user()
            ->redirectToBillingPortal(route('front.account.settings'));
    }
}
