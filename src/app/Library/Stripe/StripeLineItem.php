<?php

namespace App\Library\Stripe;

use App\Entities\AcceptedCurrencyType;

final class StripeLineItem
{
    private $amount;
    private $currency;
    private $name;
    private $quantity;
    private $description;
    private $images;

    public function __construct(int $amount, AcceptedCurrencyType $currency, string $name, int $quantity, ?string $description = null, ?array $images = null)
    {
        $this->amount = $amount;
        $this->currency = $currency;
        $this->name = $name;
        $this->quantity = $quantity;
        $this->description = $description;
        $this->images = $images;
    }

    public function toArray() : array
    {
        $array = [
            'name' => $this->name,
            'amount' => $this->amount,
            'currency' => $this->currency->valueOf(),
            'quantity' => $this->quantity,
        ];
        
        if ($this->description !== null) {
            $array['description'] = $this->description;
        }
        if ($this->images !== null) {
            $array['images'] = $this->images;
        }

        return $array;
    }
}