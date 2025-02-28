<?php

namespace App\Domains\Donations\Data;

enum PaymentType
{
    case ONE_OFF;
    case SUBSCRIPTION;
    case UNKNOWN;

    public static function fromString(string $string): PaymentType
    {
        return match ($string) {
            'one_time' => PaymentType::ONE_OFF,
            'recurring' => PaymentType::SUBSCRIPTION,
            default => PaymentType::UNKNOWN,
        };
    }

    public function isSubscription(): bool
    {
        return $this == PaymentType::SUBSCRIPTION;
    }
}
