<?php

namespace App\Http\Controllers;

use App\Entities\Donations\Models\Donation;
use App\Entities\Payments\AccountPaymentType;
use App\Entities\Payments\Models\AccountPayment;
use App\Library\Stripe\StripeHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard as Auth;
use App\Library\Discourse\Api\DiscourseUserApi;
use App\Entities\Groups\Repositories\GroupRepository;
use App\Http\WebController;

final class DonationController extends WebController
{
    /**
     * Amount that needs to be donated to be granted
     * lifetime perks
     */
    const LIFETIME_REQUIRED_AMOUNT = 30;

    /**
     * @var StripeHandler
     */
    private $stripeHandler;

    /**
     * @var DiscourseUserApi
     */
    private $discourseUserApi;


    /**
     * @var GroupRepository
     */
    private $groupRepository;

    /**
     * @var Auth
     */
    private $auth;


    public function __construct(
        StripeHandler $stripeHandler,
        DiscourseUserApi $discourseUserApi,
        GroupRepository $groupRepository,
        Auth $auth
    ) {
        $this->stripeHandler = $stripeHandler;
        $this->discourseUserApi = $discourseUserApi;
        $this->groupRepository = $groupRepository;
        $this->auth = $auth;
    }

    public function index()
    {
        return view('front.pages.donate.donate');
    }

    public function store(Request $request)
    {
        $email       = $request->get('stripe_email');
        $stripeToken = $request->get('stripe_token');
        $amount      = $request->get('stripe_amount_in_cents');

        if ($amount <= 0) {
            abort(401, "Attempted to donate zero dollars");
        }

        $account = $this->auth->user();
        $accountId = $account !== null ? $account->getKey() : null;

        try {
            $donation = $this->donate($stripeToken, $email, $amount, $accountId);
        } catch (\Stripe\Error\Card $exception) {
            $body = $exception->getJsonBody();
            $message = $body['error']['message'];

            return view('front.pages.donate.donate-error', [
                'message' => $message
            ]);
        } catch (\Stripe\Error\Base $e) {
            app('sentry')->captureException($e);
            return view('front.pages.donate.donate-error', [
                'message' => "There was a problem processing your transaction, please try again later."
            ]);
        }

        // add user to donator group if they're logged in
        if ($account !== null) {
            $donatorGroup = $this->groupRepository->getGroupByName("donator");
            $donatorGroupId = $donatorGroup->getKey();

            if ($account->groups->contains($donatorGroupId) === false) {
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

        try {
            $this->stripeHandler->charge(
                $amountInCents,
                $stripeToken,
                null,
                $email,
                'PCB Contribution'
            );
        }
        catch (\Exception $e) {
            throw $e;
        }

        $isLifetime = $amountInDollars >= self::LIFETIME_REQUIRED_AMOUNT;

        $donationExpiry = null;
        if (!$isLifetime) {
            $donationExpiry = now()->addMonths(floor($amountInDollars / 3));
        }

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
            return $donation;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
