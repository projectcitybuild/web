<?php

namespace App\Http\Controllers\Front\Account;

use App\Http\Controllers\WebController;
use Illuminate\Http\Request;

final class AccountBillingController extends WebController
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->stripe_id === null) {
            $user->createAsStripeCustomer();
        }

        return $user->redirectToBillingPortal(route('front.account.profile'));
    }
}
