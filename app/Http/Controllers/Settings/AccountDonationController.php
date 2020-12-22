<?php

namespace App\Http\Controllers\Settings;


use App\Http\WebController;

use Illuminate\Http\Request;

final class AccountDonationController extends WebController
{
    public function index(Request $request)
    {
        $request->user()->load(['donationPerks', 'donationPerks.donation']);
        $donationPerks = $request->user()->donationPerks;
        return view('front.pages.account.account-donations')->with(compact('donationPerks'));
    }
}
