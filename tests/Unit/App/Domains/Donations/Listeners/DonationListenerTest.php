<?php

use App\Core\Domains\Payment\Events\PaymentCreated;
use App\Domains\Donations\Listeners\DonationListener;
use App\Domains\Donations\UseCases\ProcessDonation;
use App\Models\Account;
use App\Models\DonationTier;
use App\Models\Payment;
use App\Models\Role;
use App\Models\StripeProduct;
use Mockery\MockInterface;

beforeEach(function () {});

it('calls ProcessDonation on PaymentCreated event', function () {
    $donationTier = DonationTier::factory()
        ->for(Role::factory())
        ->create();

    $product = StripeProduct::factory()
        ->for($donationTier)
        ->create();

    $payment = Payment::factory()
        ->for(Account::factory())
        ->product($product)
        ->create();

    $processDonation = mock(ProcessDonation::class, fn (MockInterface $mock) => $mock->shouldReceive('execute')
        ->with(
            $payment->account,
            $payment->stripe_product,
            $payment->stripe_price,
            $payment->getKey(),
            $payment->original_unit_amount,
            $payment->unit_quantity,
        )
        ->once()
        ->andReturnNull(),
    );
    $listener = new DonationListener($processDonation);
    $listener->handle(new PaymentCreated($payment));
});
