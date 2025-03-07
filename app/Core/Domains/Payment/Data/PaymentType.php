<?php

namespace App\Core\Domains\Payment\Data;

enum PaymentType
{
    case ONE_TIME;
    case SUBSCRIPTION;
    case UNKNOWN;

    public static function fromString(string $string): PaymentType
    {
        return match ($string) {
            'one_time' => PaymentType::ONE_TIME,
            'recurring' => PaymentType::SUBSCRIPTION,
            default => PaymentType::UNKNOWN,
        };
    }

    public function isSubscription(): bool
    {
        return $this == PaymentType::SUBSCRIPTION;
    }
}
