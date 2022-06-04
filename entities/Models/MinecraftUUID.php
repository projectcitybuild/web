<?php

namespace Entities\Models;

use function collect;

final class MinecraftUUID
{
    private string $uuid;

    public function __construct(string $rawValue)
    {
        $this->uuid = str_replace(search: '-', replace: '', subject: $rawValue);
    }

    public function __toString(): string
    {
        return $this->uuid;
    }

    public function rawValue(): string
    {
        return $this->uuid;
    }

    public function hyphenated(): string
    {
        $parts = [
            substr(string: $this->uuid, offset: 0, length: 8),
            substr(string: $this->uuid, offset: 8, length: 4),
            substr(string: $this->uuid, offset: 12, length: 4),
            substr(string: $this->uuid, offset: 16, length: 4),
            substr(string: $this->uuid, offset: 20),
        ];
        return collect($parts)->join(glue: '-');
    }
}
