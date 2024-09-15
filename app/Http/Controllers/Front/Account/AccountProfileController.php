<?php

namespace App\Http\Controllers\Front\Account;

use App\Http\Controllers\WebController;
use App\Models\Badge;
use Illuminate\Http\Request;

class AccountProfileController extends WebController
{
    public function show(Request $request)
    {
        $account = $request->user();
        $badges = Badge::orderBy('display_name', 'asc')
            ->get();

        return view('front.pages.account.profile')
            ->with(compact('account', 'badges'));
    }
}
