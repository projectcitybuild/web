<?php

namespace App\Http\Controllers;

use App\Http\WebController;
use Domain\Payments\DonationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

final class DonationController extends WebController
{
    public function index()
    {
        return view('v2.front.pages.donate.donate');
    }

    public function success()
    {
        return view('v2.front.pages.donate.donate-thanks');
    }

    public function store(Request $request, DonationService $donationService)
    {
        $validator = Validator::make($request->all(), [
            'price_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            // TODO
            return redirect()->back();
        }

        $stripePriceId = $request->input('price_id');

        $checkoutURL = $donationService->startCheckout($stripePriceId);

        // Redirect to Stripe Checkout page
        return redirect($checkoutURL);
    }
}
