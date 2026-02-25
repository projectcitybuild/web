<?php

namespace App\Domains\Donations\Data;

class AnnualDonations
{
    public function __construct(
        public readonly int $fundingGoalAmount,
        public readonly int $remainingAmountToReachGoal,
        public readonly int $amountRaisedThisYear,
        public readonly int $donationCountThisYear,
    ) {}
}
