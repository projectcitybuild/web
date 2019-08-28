<?php
namespace App\Services\Donations;

use App\Library\Stripe\StripeHandler;
use App\Entities\Eloquent\Donations\Repositories\DonationRepository;
use App\Entities\Eloquent\Payments\Repositories\AccountPaymentRepository;
use Illuminate\Database\Connection;
use App\Entities\Eloquent\Payments\AccountPaymentType;
use App\Library\Discourse\Api\DiscourseAdminApi;
use App\Library\Discourse\Api\DiscourseUserApi;

class DonationCreationService
{
    /**
     * Amount that needs to be donated to be granted
     * lifetime perks
     */
    const LIFETIME_REQUIRED_AMOUNT = 30;

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

    /**
     * @var DiscourseAdminApi
     */
    private $discourseAdminApi;

    /**
     * @var DiscourseUserApi
     */
    private $discourseUserApi;


    public function __construct(DonationRepository $donationRepository,
                                AccountPaymentRepository $paymentRepository,
                                Connection $connection,
                                StripeHandler $stripeHandler,
                                DiscourseAdminApi $discourseAdminApi,
                                DiscourseUserApi $discourseUserApi)
    {
        $this->donationRepository = $donationRepository;
        $this->paymentRepository = $paymentRepository;
        $this->connection = $connection;
        $this->stripeHandler = $stripeHandler;
        $this->discourseAdminApi = $discourseAdminApi;
        $this->discourseUserApi = $discourseUserApi;
    }


    public function donate(string $stripeToken, string $email, int $amountInCents, ?int $pcbId = null)
    {
        $amountInDollars = (float)($amountInCents / 100);

        try {
            $charge = $this->stripeHandler->charge($amountInCents, $stripeToken, null, $email, 'PCB Contribution');
        } catch (\Exception $e) {
            throw $e;
        }

        $isLifetime = $amountInDollars >= self::LIFETIME_REQUIRED_AMOUNT;
        if ($isLifetime) {
            $donationExpiry = null;
        } else {
            $donationExpiry = now()->addMonths(floor($amountInDollars / 3));
        }

        $this->connection->beginTransaction();
        try {
            $donation = $this->donationRepository->create($pcbId,
                                                          $amountInDollars,
                                                          $donationExpiry,
                                                          $isLifetime);

            $payment = $this->paymentRepository->create(new AccountPaymentType(AccountPaymentType::Donation),
                                                        $donation->getKey(),
                                                        $amountInCents,
                                                        $stripeToken,
                                                        $pcbId,
                                                        true);
            $this->connection->commit();
            return $donation;
            
        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }
}