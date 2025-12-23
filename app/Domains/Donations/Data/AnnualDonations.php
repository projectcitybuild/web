<?php

namespace App\Domains\Donations\Data;

class AnnualDonations
{
    public function __construct(
        public readonly int $amountRequired,
        public readonly int $raisedThisYear,
        public readonly int $raisedLastYear,
    ) {}
}
