<?php
namespace App\Services\Donations;

use App\Entities\Donations\Models\Donation;
use App\Entities\Payments\Models\AccountPayment;
use App\Library\Stripe\StripeHandler;
use App\Entities\Payments\AccountPaymentType;
use Illuminate\Support\Facades\DB;

class DonationCreationService
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


    public function __construct(StripeHandler $stripeHandler)
    {
        $this->stripeHandler = $stripeHandler;
    }


    public function donate(string $stripeToken, string $email, int $amountInCents, ?int $pcbId = null)
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