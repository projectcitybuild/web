<?php

namespace Helpers;

/**
 * Allows an enum to be "imploded" to a string
 */
trait ValueJoinable
{
    /**
     * @param  self  ...$cases Enum cases to join
     * @return string Comma delimited string of values
     */
    public static function joined(self ...$cases): string
    {
        return collect($cases)
            ->map(fn ($s) => $s->value)
            ->join(glue: ',');
    }

    /**
     * @return string Comma delimited string of all case values
     */
    public static function allJoined(): string
    {
        return collect(self::cases())
            ->map(fn ($c) => $c->value)
            ->join(glue: ',');
    }
}
