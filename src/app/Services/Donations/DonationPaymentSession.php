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

    public static function createFromJSON(string $jsonString) : DonationPaymentSession
    {
        $json = json_decode($jsonString);

        return new DonationPaymentSession(
            $json['accountId'],
            $json['amountInCents']
        );
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    public function getAccountId() : ?int
    {
        return $this->accountId;
    }

    public function getAmountInCents() : int
    {
        return $this->amountInCents;
    }
}