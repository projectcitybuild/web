<?php

namespace App\Services\Donations;

final class DonationPaymentSession implements \JsonSerializable
{
    private $accountId;
    private $amountInCents;

    public function __construct(?int $accountId, int $amountInCents)
    {
        $this->accountId = $accountId;
        $this->amountInCents = $amountInCents;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}