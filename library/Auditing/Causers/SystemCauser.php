<?php

namespace Library\Auditing\Causers;

use ReflectionEnum;

enum SystemCauser
{
    case STRIPE_WEBHOOK;

    public function displayName(): string
    {
        return match ($this) {
            self::STRIPE_WEBHOOK => 'Stripe',
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
