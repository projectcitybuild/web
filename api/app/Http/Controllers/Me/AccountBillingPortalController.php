<?php

namespace App\Http\Controllers\Me;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AccountBillingPortalController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $returnUrl = $request->get('return_url');
        $user = $request->user();

        if ($user->stripe_id === null) {
            $user->createAsStripeCustomer();
        }

        return response()->json([
            'url' => $user->billingPortalUrl($returnUrl),
        ]);
    }
}
