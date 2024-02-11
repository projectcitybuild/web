<?php

namespace App\Models;

class MinecraftUUID
{
    public function __construct(
        private string $uuid,
    ) {
        // TODO: perform some validation
    }

    public function rawValue(): string
    {
        return str_replace(search: '-', replace: '', subject: $this->uuid);
    }
}
