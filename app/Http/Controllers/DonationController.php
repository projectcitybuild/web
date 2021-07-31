<?php

namespace App\Http\Controllers;

use App\Entities\Payments\Models\AccountPaymentSession;
use App\Http\WebController;
use App\Library\Stripe\StripeHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

final class DonationController extends WebController
{
    public function index()
    {
        return view('v2.front.pages.donate.donate');
    }

    public function success()
    {
        return view('v2.front.pages.donate.donate-thanks');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'price_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            // TODO
            return redirect()->back();
        }

        $stripePriceId = $request->input('price_id');
        $accountId = $request->get('account_id');

        $stripe = new StripeHandler();

        $pcbSessionUuid = Str::uuid();
        $stripeSession = $stripe->createCheckoutSession($pcbSessionUuid, $stripePriceId);

        $pcbPaymentSession = AccountPaymentSession::create([
            'session_id' => $pcbSessionUuid->toString(),
            'account_id' => $accountId,
            'is_processed' => false,
        ]);

        Log::debug('Generated payment session', ['session' => $pcbPaymentSession]);

        // Redirect to Stripe Checkout page
        return redirect($stripeSession->url);
    }
}
