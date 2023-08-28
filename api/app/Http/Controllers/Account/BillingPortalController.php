<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

final class BillingPortalController extends Controller
{
    public function index(Request $request)
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
