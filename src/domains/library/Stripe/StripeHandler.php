<?php
namespace Domains\Library\Stripe;

use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Customer;


class StripeHandler
{
    private $currency = 'aud';

    /**
     * account_id if available
     *
     * @var int
     */
    private $pcbAccountId;


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

    public function charge(int $amount, string $token = null, ?int $pcbId = null, string $receiptEmail = null, string $description = null) : Charge
    {
        return Charge::create([
            'amount' => $amount,
            'source' => $token,
            'receipt_email' => $receiptEmail,
            'description' => $description,
            'customer' => $pcbId,
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
}