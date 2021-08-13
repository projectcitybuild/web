<?php

namespace App\Http\Controllers;

use App\Entities\Donations\Models\Donation;
use App\Entities\Donations\Models\DonationTier;
use App\Http\WebController;
use Domain\Donations\DonationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

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

    public function checkout(Request $request, DonationService $donationService)
    {
        $validator = Validator::make($request->all(), [
            'price_id' => 'required|string',
            'product_id' => 'required|string',
            'is_subscription' => 'required',
            'quantity' => 'required|int|between:1,999',
        ]);
        if ($validator->fails()) {
            // TODO
            return redirect()->back();
        }

        $priceId = $request->input('price_id');
        $productId = $request->input('product_id');
        $numberOfMonthsToBuy = $request->input('quantity');
        $isSubscription = boolval($request->input('is_subscription'));

        $donationTier = DonationTier::where('stripe_product_id', $productId)->first();

        if ($donationTier === null) {
            // TODO
            return redirect()->back();
        }

        if ($request->user() === null) {
            // TODO
        }

        if ($isSubscription) {
            return $request->user()
                ->newSubscription($donationTier->name, $priceId)
                ->checkout([
                    'success_url' => route('front.donate.success'),
                    'cancel_url' => route('front.donate'),
                ]);
        } else {
            return $request->user()
                ->checkout([$priceId => $numberOfMonthsToBuy], [
                    'success_url' => route('front.donate.success'),
                    'cancel_url' => route('front.donate'),
                ]);
        }
    }
}
