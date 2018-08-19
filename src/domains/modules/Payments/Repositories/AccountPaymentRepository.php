<?php
namespace Domains\Modules\Payments\Repositories;

use Domains\Repository;
use Carbon\Carbon;
use Domains\Modules\Payments\Models\AccountPayment;
use Domains\Modules\Payments\AccountPaymentType;

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
