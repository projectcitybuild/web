<?php

namespace App\Http\Controllers\Settings;

use App\Http\WebController;
use Illuminate\Http\Request;

final class AccountSubscriptionController extends WebController
{
    public function index(Request $request)
    {
        return view('v2.front.pages.account.account-subscriptions')->with([
            'subscriptions' => collect(),
        ]);
    }
}
