<?php

namespace App\Http\Controllers\Front;

use App\Domains\Donations\UseCases\BeginCheckout;
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
        BeginCheckout $beginCheckoutUseCase,
    ) {
        $account = $request->user();
        if ($account === null) {
            return redirect()->back()->withErrors(['You must be logged-in to make a purchase']);
        }

        $request->validate([
            'price_id' => 'required|string',
            'quantity' => 'int|between:1,999',
        ]);

        return $beginCheckoutUseCase->execute(
            account: $account,
            priceId: $request->input('price_id'),
            numberOfMonthsToBuy: $request->input('quantity'),
        );
    }
}
