<?php

namespace App\Domains\Donations\Listeners;

use App\Core\Domains\Payment\Events\PaymentCreated;
use App\Domains\Donations\UseCases\ProcessDonation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class DonationListener implements ShouldQueue
{
    public function __construct(
        private readonly ProcessDonation $processDonation,
    ) {}

    public function handle(PaymentCreated $event): void
    {
        $payment = $event->payment;

        if ($payment->account === null) {
            Log::warning('Payment had no account to fulfill a donation', compact('event'));
        }
        $this->processDonation->execute(
            account: $payment->account,
            productId: $payment->stripe_product,
            priceId: $payment->stripe_price,
            paymentId: $payment->id,
            unitAmount: $payment->original_unit_amount,
            unitQuantity: $payment->unit_quantity,
        );
    }
}
