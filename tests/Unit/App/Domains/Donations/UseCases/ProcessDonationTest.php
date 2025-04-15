<?php

use App\Domains\Donations\Notifications\DonationPerkStartedNotification;
use App\Domains\Donations\UseCases\ProcessDonation;
use App\Models\Account;
use App\Models\Donation;
use App\Models\DonationPerk;
use App\Models\DonationTier;
use App\Models\Group;
use App\Models\Payment;
use App\Models\StripeProduct;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Group::factory()->donor()->create();

    $this->donationTier = DonationTier::factory()
        ->for(Group::factory())
        ->create();

    $this->product = StripeProduct::factory()
        ->for($this->donationTier)
        ->create();

    $this->account = Account::factory()
        ->create();

    $this->payment = Payment::factory()
        ->for($this->account)
        ->product($this->product)
        ->create();

    Notification::fake();
});

it('creates Donation', function () {
    $processDonation = new ProcessDonation();
    $processDonation->execute(
        account: $this->account,
        productId: $this->payment->stripe_product,
        priceId: $this->payment->stripe_price,
        paymentId: $this->payment->getKey(),
        unitAmount: 500,
        unitQuantity: 2,
    );

    $this->assertDatabaseHas(Donation::tableName(), [
        'account_id' => $this->account->getKey(),
        'amount' => 1000,
        'payment_id' => $this->payment->getKey(),
    ]);
});

describe('DonationPerk', function () {
    describe('created', function () {
        it('if absent', function () {
            $this->freezeTime(function (Carbon $now) {
                $processDonation = new ProcessDonation();
                $processDonation->execute(
                    account: $this->account,
                    productId: $this->payment->stripe_product,
                    priceId: $this->payment->stripe_price,
                    paymentId: $this->payment->getKey(),
                    unitAmount: 500,
                    unitQuantity: 2,
                );

                $this->assertDatabaseHas(DonationPerk::tableName(), [
                    'donation_tier_id' => $this->donationTier->getKey(),
                    'account_id' => $this->account->getKey(),
                    'is_active' => true,
                    'expires_at' => $now->addMonths(2),
                ]);
            });
        });

        it('if expired exists', function ($perk) {
            $this->freezeTime(function (Carbon $now) {
                $processDonation = new ProcessDonation();
                $processDonation->execute(
                    account: $this->account,
                    productId: $this->payment->stripe_product,
                    priceId: $this->payment->stripe_price,
                    paymentId: $this->payment->getKey(),
                    unitAmount: 500,
                    unitQuantity: 2,
                );

                $this->assertDatabaseHas(DonationPerk::tableName(), [
                    'donation_tier_id' => $this->donationTier->getKey(),
                    'account_id' => $this->account->getKey(),
                    'is_active' => true,
                    'expires_at' => $now->addMonths(2),
                ]);
            });
        })->with([
            fn() => DonationPerk::factory()
                ->for($this->account)
                ->for($this->donationTier)
                ->for(Donation::factory()->for($this->account))
                ->expired()
                ->create(),
            fn() => DonationPerk::factory()
                ->for($this->account)
                ->for($this->donationTier)
                ->for(Donation::factory()->for($this->account))
                ->inactive()
                ->create(),
        ]);

        it('sends "started" notification', function () {
            $processDonation = new ProcessDonation();
            $processDonation->execute(
                account: $this->account,
                productId: $this->payment->stripe_product,
                priceId: $this->payment->stripe_price,
                paymentId: $this->payment->getKey(),
                unitAmount: 500,
                unitQuantity: 2,
            );
            Notification::assertSentTo(
                $this->account,
                DonationPerkStartedNotification::class,
            );
        });
    });

    describe('if active exists', function () {
        it('does not send "started" notifications', function () {
            DonationPerk::factory()
                ->for($this->account)
                ->for($this->donationTier)
                ->for(Donation::factory()->for($this->account))
                ->create();

            $processDonation = new ProcessDonation();
            $processDonation->execute(
                account: $this->account,
                productId: $this->payment->stripe_product,
                priceId: $this->payment->stripe_price,
                paymentId: $this->payment->getKey(),
                unitAmount: 500,
                unitQuantity: 2,
            );
            Notification::assertNothingSent();
        });

        it('extends expiry date', function () {
            $this->freezeTime(function (Carbon $now) {
                $donation = Donation::factory()
                    ->for($this->account)
                    ->create();

                $originalExpiry = now()->addDays(2);
                $perk = DonationPerk::factory()
                    ->for($this->account)
                    ->for($this->donationTier)
                    ->for($donation)
                    ->create(['expires_at' => $originalExpiry]);

                $processDonation = new ProcessDonation();
                $processDonation->execute(
                    account: $this->account,
                    productId: $this->payment->stripe_product,
                    priceId: $this->payment->stripe_price,
                    paymentId: $this->payment->getKey(),
                    unitAmount: 500,
                    unitQuantity: 2,
                );

                $this->assertDatabaseHas(DonationPerk::tableName(), [
                    DonationPerk::primaryKey() => $perk->getKey(),
                    'donation_id' => $donation->getKey(),
                    'donation_tier_id' => $this->donationTier->getKey(),
                    'account_id' => $this->account->getKey(),
                    'is_active' => true,
                    'expires_at' => $originalExpiry->addMonths(2),
                ]);
            });
        });

        it('extends from now if already past expiry dte', function () {
            $this->freezeTime(function (Carbon $now) {
                $donation = Donation::factory()
                    ->for($this->account)
                    ->create();

                $originalExpiry = now()->subDay();
                $perk = DonationPerk::factory()
                    ->for($this->account)
                    ->for($this->donationTier)
                    ->for($donation)
                    ->create(['expires_at' => $originalExpiry]);

                $processDonation = new ProcessDonation();
                $processDonation->execute(
                    account: $this->account,
                    productId: $this->payment->stripe_product,
                    priceId: $this->payment->stripe_price,
                    paymentId: $this->payment->getKey(),
                    unitAmount: 500,
                    unitQuantity: 2,
                );

                $this->assertDatabaseHas(DonationPerk::tableName(), [
                    DonationPerk::primaryKey() => $perk->getKey(),
                    'donation_id' => $donation->getKey(),
                    'donation_tier_id' => $this->donationTier->getKey(),
                    'account_id' => $this->account->getKey(),
                    'is_active' => true,
                    'expires_at' => $now->addMonths(2),
                ]);
            });
        });
    });
});
