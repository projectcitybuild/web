<?php

namespace App\Http\Controllers\Front;

use App\Domains\Donations\UseCases\BeginDonationCheckout;
use App\Http\Controllers\WebController;
use Illuminate\Http\Request;

final class DonationController extends WebController
{
    public function index()
    {
        return view('front.pages.donate.donate', [
            'target_funding' => config('donations.target_funding'),
        ]);
    }

    public function success()
    {
        return view('front.pages.donate.donate-thanks');
    }

    public function checkout(
        Request $request,
        BeginDonationCheckout $beginCheckoutUseCase,
    ) {
        $account = $request->user();
        if ($account === null) {
            return redirect()->back()->withErrors(['You must be logged-in to make a purchase']);
        }

        $validated = $request->validate([
            'price_id' => ['required', 'string'],
            'quantity' => ['int', 'between:1,999'],
        ]);

        return $beginCheckoutUseCase->execute(
            account: $account,
            priceId: $validated['price_id'],
            numberOfMonthsToBuy: $validated['quantity'] ?? 1,
        );
    }
}
