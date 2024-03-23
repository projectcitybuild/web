<?php

namespace App\Http\Controllers\Donate;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DonateController extends Controller
{
    public function single(Request $request): JsonResponse
    {
        $request->validate([
            'price_id' => ['required', 'string'],
            'months' => ['required', 'int', 'between:1,999'],
            'success_url' => ['required', 'url'],
            'cancel_url' => ['required', 'url'],
        ]);

        $checkout = $request
            ->user()
            ->checkout(
                [$request->get('price_id') => $request->get('months')],
                $request->only(['success_url', 'cancel_url']),
            )
            ->toArray(); // Prevent automatic redirection to Stripe Checkout

        return response()->json([
            'checkout_url' => $checkout['url'],
        ]);
    }

    public function subscription(Request $request): JsonResponse
    {
        $request->validate([
            'price_id' => ['required', 'string'],
        ]);

        $checkout = $request
            ->user()
            ->newSubscription('TODO product name', $request->get('price_id'))
            ->checkout($request->only(['success_url', 'cancel_url']))
            ->toArray(); // Prevent automatic redirection to Stripe Checkout

        return response()->json([
            'checkout_url' => $checkout['url'],
        ]);
    }
}
