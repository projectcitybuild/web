<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;

final class StripeWebhookController extends CashierController
{

}
