<?php

namespace App\Domains\Donations\Listeners;

use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookReceived;

class StripeListener implements ShouldQueue, ShouldHandleEventsAfterCommit
{
    public function handle(WebhookReceived $event): void
    {
        if ($event->payload['type'] === 'invoice.payment_succeeded') {
            Log::info('Handling invoice.payment_succeeded webhook event', context: ['event' => $event]);

            // TODO
        }
    }
}
