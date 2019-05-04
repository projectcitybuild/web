<?php

namespace App\Entities;

use App\Enum;

/**
 * Currencies accepted by our payment/donation system
 */
final class AcceptedCurrencyType extends Enum
{
    public const CURRENCY_AUD = 'aud';
}