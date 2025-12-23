<?php

namespace App\Core\Domains\Auditing\Causers;

class SystemCauseResolver
{
    private static SystemCauser $causer;

    public static function getCauserName(): ?string
    {
        return isset(self::$causer) ? self::$causer->name : null;
    }

    public static function setCauser(SystemCauser $causer): void
    {
        self::$causer = $causer;
    }
}
