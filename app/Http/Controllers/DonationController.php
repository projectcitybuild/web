<?php

namespace App\Http\Controllers;

use App\Entities\Donations\Models\DonationTier;
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

    public function checkout(Request $request, DonationService $donationService)
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

        $donationTier = DonationTier::where('stripe_payment_price_id', $productId)
            ->orWhere('stripe_subscription_price_id', $productId)
            ->first();

        if ($donationTier === null) {
            // TODO
            return redirect()->back();
        }

        if ($request->user() === null) {
            // TODO
        }

        $isSubscription = $donationTier->stripe_subscription_price_id == $productId;

        if ($isSubscription) {
            return $request->user()
                ->newSubscription($donationTier->name, $productId)
                ->checkout([
                    'success_url' => route('front.donate.success'),
                    'cancel_url' => route('front.donate'),
                ]);
        } else {
            return $request->user()
                ->checkout([$productId => $numberOfMonthsToBuy], [
                    'success_url' => route('front.donate.success'),
                    'cancel_url' => route('front.donate'),
                ]);
        }
    }
}
