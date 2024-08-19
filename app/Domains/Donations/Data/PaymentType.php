<?php

namespace App\Domains\Donations\Data;

enum PaymentType
{
    case ONE_OFF;
    case SUBSCRIPTION;

    public function isSubscription(): bool
    {
        return $this == PaymentType::SUBSCRIPTION;
    }
}
