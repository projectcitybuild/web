<?php

namespace App\Http\Controllers;

use App\Entities\Donations\Models\Donation;
use App\Entities\Donations\Models\DonationTier;
use App\Http\WebController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Stripe\StripeClient;

final class DonationController extends WebController
{
    public function index()
    {
        // TODO: combine with HomeController
        $requiredAmount = 1000;

        $now = Carbon::now();
        $thisYear = $now->year;
        $totalDonationsThisYear = Donation::whereYear('created_at', $thisYear)->sum('amount');
        $percentage = round($totalDonationsThisYear / $requiredAmount * 100);

        return view('v2.front.pages.donate.donate', [
            'donations' => [
                'raised_this_year' => $totalDonationsThisYear ?: 0,
                'percentage' => max(1, $percentage) ?: 0,
            ],
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
        $productId = $price['product'];
        $isSubscription = $price['recurring'] !== null;

        $donationTier = DonationTier::where('stripe_product_id', $productId)->first();
        if ($donationTier === null) {
            return redirect()->back()->withErrors(['Could not find donation tier. Please contact a staff member if you think this is a mistake']);
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
