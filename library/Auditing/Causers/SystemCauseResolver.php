<?php

namespace Library\Auditing\Causers;

class SystemCauseResolver
{
    private static SystemCauser $causer;

    /**
     * @return null|string
     */
    public static function getCauserName(): ?string
    {
        return isset(self::$causer) ? self::$causer->name : null;
    }

    public static function setCauser(SystemCauser $causer): void
    {
        self::$causer = $causer;
    }
}
