<?php
namespace App\Services\Donations;

use App\Library\Stripe\StripeHandler;
use App\Entities\Donations\Repositories\DonationRepository;
use App\Entities\Payments\Repositories\AccountPaymentRepository;
use App\Entities\Payments\AccountPaymentType;
use Illuminate\Support\Facades\DB;

final class DonationCreationService
{
    /**
     * Amount that needs to be donated to be granted
     * lifetime perks
     */
    private const LIFETIME_REQUIRED_AMOUNT = 30;

     /**
     * @var DonationRepository
     */
    private $donationRepository;

    /**
     * @var AccountPaymentRepository
     */
    private $paymentRepository;

    /**
     * @var StripeHandler
     */
    private $stripeHandler;


    public function __construct(
        DonationRepository $donationRepository,
        AccountPaymentRepository $paymentRepository,
        StripeHandler $stripeHandler
    ) {
        $this->donationRepository = $donationRepository;
        $this->paymentRepository = $paymentRepository;
        $this->stripeHandler = $stripeHandler;
    }

    public function donate(string $stripeToken, string $email, int $amountInCents, ?int $pcbId = null)
    {
        $amountInDollars = (float)($amountInCents / 100);

        try {
            $this->stripeHandler->charge($amountInCents, $stripeToken, null, $email, 'PCB Contribution');
        } catch (\Exception $e) {
            throw $e;
        }

        $isLifetime = $amountInDollars >= self::LIFETIME_REQUIRED_AMOUNT;
        if ($isLifetime) {
            $donationExpiry = null;
        } else {
            $donationExpiry = now()->addMonths(floor($amountInDollars / 3));
        }

        DB::beginTransaction();
        try {
            $donation = $this->donationRepository->create(
                $pcbId,
                $amountInDollars,
                $donationExpiry,
                $isLifetime
            );
            $this->paymentRepository->create(
                new AccountPaymentType(AccountPaymentType::Donation),
                $donation->getKey(),
                $amountInCents,
                $stripeToken,
                $pcbId,
                true
            );

            DB::commit();

            return $donation;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}