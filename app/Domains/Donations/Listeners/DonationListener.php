<?php

namespace App\Domains\Donations\Listeners;

use App\Core\Domains\Payment\Events\PaymentCreated;
use App\Domains\Donations\UseCases\ProcessDonation;
use App\Domains\Donations\UseCases\RecordPayment;
use Stripe\StripeClient;


class DonationListener
{
    public function __construct(
        private readonly RecordPayment $recordPayment,
        private readonly ProcessDonation $processDonation,
    ) {}

    public function handle(PaymentCreated $event): void
    {
        
    }
}
