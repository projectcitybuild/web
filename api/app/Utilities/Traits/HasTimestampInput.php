<?php

namespace App\Utilities\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

trait HasTimestampInput
{
    public static function timestamp(?String $from): ?Carbon
    {
        if ($from !== null && ! empty($from)) {
            $from = Carbon::createFromTimestamp($from);
        }
        return $from;
    }
}
