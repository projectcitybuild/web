<?php

namespace App\Http\Controllers\Api;

use App\Http\ApiController;
use App\Library\Stripe\StripeHandler;
use Illuminate\Http\Request;

final class DonationController extends ApiController
{
    /**
     * @var StripeHandler
     */
    private $stripeHandler;

    public function __construct(StripeHandler $stripeHandler)
    {
        $this->stripeHandler = $stripeHandler;
    }

    public function create(Request $request)
    {
        $sessionId = $this->stripeHandler->createCheckoutSession();

        return [
            'data' => [
                'session_id' => $sessionId,
            ]
        ];
    }

    public function store(Request $request)
    {

    }
}
