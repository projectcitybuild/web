<?php

namespace App\Domains\Donations\Data;

class AnnualDonations
{
    public function __construct(
        readonly int $amountRequired,
        readonly int $raisedThisYear,
        readonly int $raisedLastYear,
    ) {}
}
