<?php

namespace App\Core\Traits;

use Illuminate\Support\Carbon;

trait HasTimestampInput
{
    public static function timestamp(?string $from): ?Carbon
    {
        if (! empty($from)) {
            $from = Carbon::createFromTimestamp($from);
        }
        return $from;
    }
}
