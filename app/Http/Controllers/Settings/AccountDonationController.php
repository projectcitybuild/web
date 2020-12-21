<?php


namespace App\Http\Controllers\Settings;


use Barryvdh\Debugbar\Controllers\BaseController;
use Illuminate\Http\Request;

final class AccountDonationController extends BaseController
{
    public function index(Request $request)
    {
        $request->user()->load(['donationPerks', 'donationPerks.donation']);
        $donationPerks = $request->user()->donationPerks;
        return view('front.pages.account.account-donations')->with(compact('donationPerks'));
    }
}
