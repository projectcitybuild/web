<?php

namespace App\Http\Controllers;

use App\Http\WebController;
use Domain\Donations\DonationService;
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

    public function moveToCheckout(Request $request, DonationService $donationService)
    {
        $validator = Validator::make($request->all(), [
            'price_id' => 'required|string',
        ]);
        if ($validator->fails()) {
            // TODO
            return redirect()->back();
        }

        $productId = $request->input('price_id');
        $checkoutURL = $donationService->startCheckoutSession($productId);

        // Redirect to Stripe Checkout page
        return redirect($checkoutURL);
    }
}
