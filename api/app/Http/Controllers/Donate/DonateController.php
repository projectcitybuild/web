<?php

namespace App\Http\Controllers\Donate;

use App\Domains\Donations\Action\GetCheckoutUrl;
use App\Domains\Donations\OneTimeCheckoutCharge;
use App\Domains\Donations\SubscriptionCheckoutCharge;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DonateController extends Controller
{
    public function single(Request $request, GetCheckoutUrl $getCheckoutUrl): JsonResponse
    {
        $validated = $request->validate([
            'price_id' => ['required', 'string'],
            'months' => ['required', 'int', 'between:1,999'],
            'success_url' => ['required', 'url'],
            'cancel_url' => ['required', 'url'],
        ]);
        $charge = new OneTimeCheckoutCharge(
            priceId: $validated['price_id'],
            months: $validated['months'],
            redirectSuccessUrl: $validated['success_url'],
            redirectCancelUrl: $validated['cancel_url'],
        );
        $redirectUrl = $getCheckoutUrl->call(
            account: $request->user(),
            charge: $charge,
        );
        return response()->json([
            'checkout_url' => $redirectUrl,
        ]);
    }

    public function subscription(Request $request, GetCheckoutUrl $getCheckoutUrl): JsonResponse
    {
        $validated = $request->validate([
            'price_id' => ['required', 'string'],
            'success_url' => ['required', 'url'],
            'cancel_url' => ['required', 'url'],
        ]);
        $charge = new SubscriptionCheckoutCharge(
            priceId: $validated['price_id'],
            productName: 'TODO product name', // TODO
            redirectSuccessUrl: $validated['success_url'],
            redirectCancelUrl: $validated['cancel_url'],
        );
        $redirectUrl = $getCheckoutUrl->call(
            account: $request->user(),
            charge: $charge,
        );
        return response()->json([
            'checkout_url' => $redirectUrl,
        ]);
    }
}
