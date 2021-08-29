<?php

namespace App\Http\Controllers;

use App\Entities\Donations\Models\DonationTier;
use App\Http\WebController;
use Illuminate\Http\Request;
use Stripe\StripeClient;

final class DonationController extends WebController
{
    public function index()
    {
        return view('v2.front.pages.donate.donate', [
            'target_funding' => config('donations.target_funding'),
        ]);
    }

    public function success()
    {
        return view('v2.front.pages.donate.donate-thanks');
    }

    public function checkout(Request $request, StripeClient $stripeClient)
    {
        $account = $request->user();
        if ($account === null) {
            return redirect()->back()->withErrors(['You must be logged-in to make a purchase']);
        }

        $request->validate([
            'price_id' => 'required|string',
            'quantity' => 'int|between:1,999',
        ]);

        $priceId = $request->input('price_id');

        $price = $stripeClient->prices->retrieve($priceId);
        $isSubscription = $price['recurring'] !== null;

        $donationTierId = $price['metadata']['donation_tier_id'];
        if ($donationTierId === null) {
            throw new \Exception('No donation_tier_id defined in Stripe metadata for this Price');
        }

        $donationTier = DonationTier::find($donationTierId);
        if ($donationTier === null) {
            throw new \Exception('No Donation Tier found for given id');
        }

        if ($isSubscription) {
            return $account
                ->newSubscription($donationTier->name, $priceId)
                ->checkout([
                    'success_url' => route('front.donate.success'),
                    'cancel_url' => route('front.donate'),
                ]);
        } else {
            $numberOfMonthsToBuy = $request->input('quantity', 1);

            return $account
                ->checkout([$priceId => $numberOfMonthsToBuy], [
                    'success_url' => route('front.donate.success'),
                    'cancel_url' => route('front.donate'),
                ]);
        }
    }
}
