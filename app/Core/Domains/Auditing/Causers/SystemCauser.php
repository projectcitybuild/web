<?php

namespace App\Core\Domains\Auditing\Causers;

use ReflectionEnum;

enum SystemCauser
{
    case STRIPE_WEBHOOK;
    case UNACTIVATED_CLEANUP;
    case PERK_EXPIRY;
    case DONOR_REWARDS;

    public function displayName(): string
    {
        return match ($this) {
            self::STRIPE_WEBHOOK => 'Stripe',
            self::UNACTIVATED_CLEANUP => 'Unactivated Cleanup',
            self::PERK_EXPIRY => 'Perk Expiry',
            self::DONOR_REWARDS => 'Donor Rewards',
        };
    }

    public static function tryFromName($name)
    {
        if ($name === null) {
            return null;
        }
        $rc = new ReflectionEnum(self::class);

        return $rc->hasCase($name) ? $rc->getConstant($name) : null;
    }
}
