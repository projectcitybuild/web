<?php

namespace App\Core\Domains\Discord\Data;

class DiscordEmbedField
{
    public function __construct(
        public string $name,
        public string $value,
        public bool $inline = false,
    ) {}

    public function toJson(): array
    {
        return [
            'name' => $this->name,
            'value' => $this->value,
            'inline' => $this->inline,
        ];
    }
}
