<?php
namespace Domains\Services\Donations;

use Domains\Library\Stripe\StripeHandler;
use Domains\Modules\Donations\Repositories\DonationRepository;
use Domains\Modules\Payments\Repositories\AccountPaymentRepository;
use Illuminate\Database\Connection;
use Domains\Modules\Payments\AccountPaymentType;

class DonationCreationService
{
     /**
     * @var DonationRepository
     */
    private $donationRepository;

    /**
     * @var AccountPaymentRepository
     */
    private $paymentRepository;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var StripeHandler
     */
    private $stripeHandler;


    public function __construct(DonationRepository $donationRepository,
                                AccountPaymentRepository $paymentRepository,
                                Connection $connection,
                                StripeHandler $stripeHandler)
    {
        $this->donationRepository = $donationRepository;
        $this->paymentRepository = $paymentRepository;
        $this->connection = $connection;
        $this->stripeHandler = $stripeHandler;
    }


    public function donate(string $stripeToken, string $email, int $amountInCents, ?int $pcbId = null)
    {
        $amountInDollars = (float)($amountInCents / 100);

        try {
            $charge = $this->stripeHandler->charge($amountInCents, $stripeToken, $pcbId, $email, 'PCB Contribution');
        } catch (\Exception $e) {
            throw $e;
        }

        $isLifetime = $amount >= 30;
        if ($isLifetime) {
            $donationExpiry = null;
        } else {
            $donationExpiry = now()->addMonths(floor($amount / 3));
        }

        $this->connection->beginTransaction();
        try {
            $donation = $this->donationRepository->create($pcbId,
                                                          $amount,
                                                          $donationExpiry,
                                                          $isLifetime);

            $payment = $this->paymentRepository->create(new AccountPaymentType(AccountPaymentType::Donation),
                                                        $donation->getKey(),
                                                        $amount / 100,
                                                        $stripeToken,
                                                        $pcbId,
                                                        true);
            $this->connection->commit();
            
        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }
}