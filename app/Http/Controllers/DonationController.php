<?php

namespace App\Http\Controllers;

use App\Http\WebController;
use Domain\Donations\DonationService;
use Illuminate\Http\Request;
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
            'quantity' => 'required|int|between:1,999',
        ]);
        if ($validator->fails()) {
            // TODO
            return redirect()->back();
        }

        $productId = $request->input('price_id');
        $numberOfMonthsToBuy = $request->input('quantity');
        $isSubscription = boolval($request->input('is_subscription'));

        if ($isSubscription) {
            // Subscriptions will auto renew after a month
            $numberOfMonthsToBuy = 1;
        }

        $checkoutURL = $donationService->startCheckoutSession($productId, $numberOfMonthsToBuy, $isSubscription);

        // Redirect to Stripe Checkout page
        return redirect($checkoutURL);
    }
}
