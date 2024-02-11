<?php

namespace App\Utilities\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

trait HasTimestampInput
{
    public static function timestamp(String $named, Collection $in): ?Carbon
    {
        $value = $in->get($named);
        if (! empty($value)) {
            $value = Carbon::createFromTimestamp($value);
        }
        return $value;
    }
}
