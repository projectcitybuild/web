<?php

namespace App\Http\Controllers\Settings;

use App\Http\WebController;
use Illuminate\Http\Request;

final class AccountDonationController extends WebController
{
    public function index(Request $request)
    {
        $user = $request->user();
        $user->load(['donations', 'donationPerks', 'donationPerks.donation', 'donationPerks.donationTier']);

        $donationPerks = $user->donationPerks;
        $donations = $user->donations;

        return view('v2.front.pages.account.account-donations')
            ->with(compact('donations', 'donationPerks'));
    }
}
