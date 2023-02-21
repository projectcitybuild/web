<?php

namespace Domain\Donations\Entities;

enum PaymentType
{
    case ONE_OFF;
    case SUBSCRIPTION;

    public function isSubscription(): bool
    {
        return $this == PaymentType::SUBSCRIPTION;
    }
}
