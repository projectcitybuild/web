<?php

namespace Library\Auditing\Causers;

class SystemCauseResolver
{
    private static SystemCauser $causer;

    /**
     * @return string
     */
    public static function getCauserName(): string
    {
        return self::$causer->name;
    }

    public static function setCauser(SystemCauser $causer): void
    {
        self::$causer = $causer;
    }
}
