<?php

namespace App\Http\Controllers;

use App\Entities\Donations\Models\Donation;
use App\Entities\Groups\Models\Group;
use App\Entities\Payments\AccountPaymentType;
use App\Entities\Payments\Models\AccountPayment;
use App\Library\Stripe\StripeHandler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\WebController;

final class DonationController extends WebController
{
    /**
     * @var StripeHandler
     */
    private $stripeHandler;


    public function __construct(StripeHandler $stripeHandler)
    {
        $this->stripeHandler = $stripeHandler;
    }

    public function index()
    {
        return view('front.pages.donate.donate');
    }

    public function store(Request $request)
    {
        $email         = $request->get('stripe_email');
        $stripeToken   = $request->get('stripe_token');
        $amountInCents = $request->get('stripe_amount_in_cents');

        if ($amountInCents <= 0) {
            return view('front.pages.donate.donate-error', [
                'message' => 'Donation must be greater than zero',
            ]);
        }

        $account = Auth::user();
        $accountId = $account !== null ? $account->getKey() : null;

        try {
            $donation = $this->donate($stripeToken, $email, $amountInCents, $accountId);
        }
        catch (\Stripe\Error\Card $exception) {
            $body = $exception->getJsonBody();
            $message = $body['error']['message'];

            return view('front.pages.donate.donate-error', [
                'message' => $message
            ]);
        }
        catch (\Stripe\Error\Base $e) {
            app('sentry')->captureException($e);
            return view('front.pages.donate.donate-error', [
                'message' => "There was a problem processing your transaction, please try again later. No charge has been made."
            ]);
        }
        catch (\Exception $e) {
            app('sentry')->captureException($e);
            return view('front.pages.donate.donate-error', [
                'message' => "An unexpected error occurred while processing your transaction, please try again later. No charge has been made."
            ]);
        }

        // Add user to Donator group if they're logged in
        if ($account !== null) {
            $donatorGroup = Group::where('name', 'donator')->first();
            $donatorGroupId = $donatorGroup->getKey();

            if (!$account->groups->contains($donatorGroupId)) {
               $account->groups()->attach($donatorGroupId);
            }
        }

        return view('front.pages.donate.donate-thanks', [
            'donation' => $donation,
        ]);
    }

    private function donate(string $stripeToken, string $email, int $amountInCents, ?int $pcbId = null)
    {
        $amountInDollars = (float)($amountInCents / 100);

        $this->stripeHandler->charge(
            $amountInCents,
            $stripeToken,
            null,
            $email,
            'PCB Contribution'
        );

        $isLifetime = $amountInDollars >= Donation::LIFETIME_REQUIRED_AMOUNT;

        $donationExpiry = null;
        if (!$isLifetime) {
            $numberOfMonthsOfPerks = floor($amountInDollars / Donation::ONE_MONTH_REQUIRED_AMOUNT);
            $donationExpiry = now()->addMonths($numberOfMonthsOfPerks);
        }

        $donation = null;
        DB::beginTransaction();
        try {
            $donation = Donation::create([
                'account_id' => $pcbId,
                'amount' => $amountInDollars,
                'perks_end_at' => $donationExpiry,
                'is_lifetime_perks' => $isLifetime,
                'is_active' => true,
            ]);

            AccountPayment::create([
                'payment_type' => AccountPaymentType::Donation,
                'payment_id' => $donation->getKey(),
                'payment_amount' => $amountInCents,
                'payment_source' => $stripeToken,
                'account_id' => $pcbId,
                'is_processed' => true,
                'is_refunded' => false,
                'is_subscription_payment' => false,
            ]);

            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $donation;
    }
}
