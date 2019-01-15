<?php
namespace Entities\Payments\Repositories;

use Application\Contracts\Repository;
use Carbon\Carbon;
use Entities\Payments\Models\AccountPayment;
use Entities\Payments\AccountPaymentType;

class AccountPaymentRepository extends Repository
{
    protected $model = AccountPayment::class;

    public function create(AccountPaymentType $paymentType,
                           int $typeId,
                           float $amount,
                           string $source,
                           ?int $accountId,
                           bool $isProcessed = false,
                           bool $isRefunded = false,
                           bool $isSubscription = false) : AccountPayment 
    {
        return $this->getModel()
            ->create([
                'payment_type' => $paymentType->valueOf(),
                'payment_id' => $typeId,
                'payment_amount' => $amount,
                'payment_source' => $source,
                'account_id' => $accountId,
                'is_processed' => $isProcessed,
                'is_refunded' => $isRefunded,
                'is_subscription_payment' => $isSubscription,
            ]);
    }
}
