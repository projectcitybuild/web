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
        $stripeSessionId = $this->donationProvider->beginDonationSession();

        return view('front.pages.donate.donate', [
            'stripe_session_id' => $stripeSessionId,
        ]);
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

        $donation = $this->donationProvider->performDonation(
            $stripeToken, 
            $email, 
            $amount, 
            $account
        );

        return view('front.pages.donate.donate-thanks', [
            'donation' => $donation,
        ]);
    }

    /**
     * Receives a callback from Stripe via webhook to notify us that
     * a user's donation has been processed
     *
     * @param Request $request
     * @return void
     */
    public function fulfillDonation(Request $request)
    {
        $this->donationProvider->fulfillDonation();   
    }
}
