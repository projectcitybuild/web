<?php

namespace Domain\Donations\Entities;

final class PaidAmount
{
    private int $amountInCents;

    public function __construct(
        float $amount,
        Denomination $denomination,
    ) {
        $this->amountInCents = match ($denomination) {
            Denomination::CENTS => $amount,
            Denomination::DOLLARS => $amount * 100,
        };
    }

    public static function fromCents(int $cents): PaidAmount
    {
        return new PaidAmount(amount: $cents, denomination: Denomination::CENTS);
    }

    public function toCents(): int
    {
        return $this->amountInCents;
    }

    public function toDollars(): float
    {
        return $this->amountInCents / 100;
    }
}
