<?php

namespace App\Http\Controllers\Front\Account;

use App\Http\Controllers\WebController;
use Illuminate\Http\Request;

final class AccountDonationController extends WebController
{
    public function index(Request $request)
    {
        $user = $request->user();
        $user->load([
            'donations',
            'donations.payment',
            'donationPerks',
            'donationPerks.donation',
            'donationPerks.donationTier',
        ]);

        $donationPerks = $user->donationPerks;
        $donations = $user->donations->sortBy('created_at');

        return view('front.pages.account.account-donations')
            ->with(compact('donations', 'donationPerks'));
    }
}
