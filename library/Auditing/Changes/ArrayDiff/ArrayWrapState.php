<?php

namespace Library\Auditing\Changes\ArrayDiff;

enum ArrayWrapState: string
{
    case ADDED = 'added';
    case KEPT = 'kept';
    case REMOVED = 'removed';

    public function isAdded(): bool
    {
        return $this == self::ADDED;
    }

    public function isKept(): bool
    {
        return $this == self::KEPT;
    }

    public function isRemoved(): bool
    {
        return $this == self::REMOVED;
    }
}
