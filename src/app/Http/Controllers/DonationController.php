<?php

namespace App\Http\Controllers;

use App\Services\Donations\DonationProvider;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard as Auth;
use App\Http\WebController;

final class DonationController extends WebController
{
    private $donationProvider;
    private $auth;
    

    public function __construct(
        DonationProvider $donationProvider,
        Auth $auth
    ) {
        $this->donationProvider = $donationProvider;
        $this->auth = $auth;
    }

    public function getView()
    {
        

        return view('front.pages.donate.donate');
    }

    public function donate(Request $request)
    {
        $email       = $request->get('stripe_email');
        $stripeToken = $request->get('stripe_token');
        $amount      = $request->get('stripe_amount_in_cents');

        if ($amount <= 0) {
            abort(400, "Attempted to donate zero dollars");
        }

        $account = $this->auth->user();

        $donation = $this->donationProvider->donate(
            $stripeToken, 
            $email, 
            $amount, 
            $account
        );

        return view('front.pages.donate.donate-thanks', [
            'donation' => $donation,
        ]);
    }
}
