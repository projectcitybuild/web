<?php

namespace App\Http\Controllers;

use App\Services\Donations\DonationProvider;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard as Auth;
use App\Http\WebController;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Http\BadRequestException;

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

    /**
     * Internal API endpoint to generate and return a Stripe payment session
     *
     * @param Request $request
     * @return void
     */
    public function makeSession(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount_in_cents' => 'bail|required|integer|gt:0',
        ]);

        if ($validator->fails()) {
            throw new BadRequestException('validation_failure', $validator->errors()->first());
        }

        $stripeSessionId = $this->donationProvider->beginDonationSession(
            $request->user(),
            $request->get('amount_in_cents')
        );

        return response()->json([
            'data' => [
                'stripe_session_id' => $stripeSessionId
            ],
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
        $signatureHeader = $request->header('Stripe-Signature');
        $payload = $request->getContent();

        $this->donationProvider->fulfillDonation($signatureHeader, $payload);
    }
}
