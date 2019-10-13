<?php

namespace App\Library\Stripe;

use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Customer;

class StripeHandler
{
    private $currency = 'aud';

    public function __construct()
    {
        $this->setApiKey();
        $this->setCurrency($this->currency);
    }

    private function setApiKey()
    {
        $key = config('services.stripe.secret');
        if (empty($key)) {
            throw new \Exception('No Stripe API secret set');
        }
        Stripe::setApiKey($key);
    }

    public function setCurrency(string $currency) : StripeHandler
    {
        $this->currency = $currency;
        return $this;
    }
    
    public function charge(int $amount, string $token = null, ?int $customerId = null, string $receiptEmail = null, string $description = null) : Charge
    {
        return Charge::create([
            'amount' => $amount,
            'source' => $token,
            'receipt_email' => $receiptEmail,
            'description' => $description,
            'customer' => $customerId,
            'currency' => $this->currency,
        ]);
    }

    public function createCustomer(string $token, string $email) : Customer
    {
        return Customer::create([
            'email' => $email,
            'source' => $token,
        ]);
    }

    /**
     * Creates a new session for using Stripe's Checkout flow
     *
     * @return string Session ID
     */
    public function createCheckoutSession(): string
    {
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'name' => 'PCB Contribution',
                    'description' => 'One time payment',
                    'images' => [],
                    'amount' => 300,
                    'currency' => $this->currency,
                    'quantity' => 1,
                ],
            ],
            'success_url' => route('front.donate'),
            'cancel_url' => route('front.donate'),
        ]);

        return $session->id;
    }
}