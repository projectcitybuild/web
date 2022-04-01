<?php

namespace Domain\Donations\Entities;

enum DonationType
{
    case ONE_OFF;
    case SUBSCRIPTION;

    public function isSubscription(): bool
    {
        return $this == DonationType::SUBSCRIPTION;
    }
}
