<?php

use App\Core\Domains\Payment\Events\PaymentCreated;
use App\Core\Domains\Payment\Listeners\StripeEventListener;
use App\Models\Account;
use App\Models\Payment;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Laravel\Cashier\Events\WebhookReceived;

beforeEach(function () {
    $this->account = Account::factory()->create([
        'stripe_id' => 'cus_JyjQ8xLdu1UmFs',
    ]);

    Notification::fake();
    Event::fake();
});

describe('one time payment', function () {
    it('creates a Payment', function () {
        $json = loadJsonFromFile('stripe/webhook-checkout-session-completed.json');
        $listener = $this->app->make(StripeEventListener::class);
        $listener->handle(new WebhookReceived($json));

        $this->assertDatabaseHas(Payment::tableName(), [
            'account_id' => $this->account->getKey(),
            'stripe_price' => 'price_1JJL5mAtUyfM4v5IJNHp1Tk2',
            'stripe_product' => 'prod_JxFaAltmFPewxs',
            'paid_unit_amount' => 300,
            'paid_currency' => 'aud',
            'original_unit_amount' => 300,
            'original_currency' => 'aud',
            'unit_quantity' => 1,
        ]);
    });

    it('dispatches a PaymentCreated event', function () {
        $json = loadJsonFromFile('stripe/webhook-checkout-session-completed.json');
        $listener = $this->app->make(StripeEventListener::class);
        $listener->handle(new WebhookReceived($json));

        Event::assertDispatched(PaymentCreated::class);
    });
});

describe('subscription payment', function () {
    it('creates a Payment', function () {
        $json = loadJsonFromFile('stripe/webhook-invoice-paid.json');
        $listener = $this->app->make(StripeEventListener::class);
        $listener->handle(new WebhookReceived($json));

        $this->assertDatabaseHas(Payment::tableName(), [
            'account_id' => $this->account->getKey(),
            'stripe_price' => 'price_1JJL5mAtUyfM4v5ISwJrrVur',
            'stripe_product' => 'prod_JxFaAltmFPewxs',
            'paid_unit_amount' => 300,
            'paid_currency' => 'AUD',
            'original_unit_amount' => 300,
            'original_currency' => 'AUD',
            'unit_quantity' => 1,
        ]);
    });

    it('dispatches a PaymentCreated event', function () {
        $json = loadJsonFromFile('stripe/webhook-invoice-paid.json');
        $listener = $this->app->make(StripeEventListener::class);
        $listener->handle(new WebhookReceived($json));

        Event::assertDispatched(PaymentCreated::class);
    });
});
