<?php

namespace App\Core\Domains\Discord\Data;

class DiscordPollMedia
{
    public function __construct(
        public string $text,
        public ?string $emojiName = null,
    ) {}

    public function toJson(): array
    {
        return [
            'poll_media' => [
                'text' => $this->text,
                'emoji' => ! empty($this->emojiName) ? ['name' => $this->emojiName] : null,
            ],
        ];
    }
}
