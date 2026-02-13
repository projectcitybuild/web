<?php

namespace App\Core\Domains\Discord\Data;

class DiscordAuthor
{
    /**
     * @see https://discord.com/developers/docs/resources/message#embed-object-embed-author-structure
     */
    public function __construct(
        public string $name,
        public ?string $url = null,
        public ?string $iconUrl = null,
    ) {}

    public static function withMinecraftAvatar(
        string $name,
        string $uuid,
        ?string $url = null,
    ) {
        return new DiscordAuthor(
            name: $name,
            url: $url,
            iconUrl: 'https://minotar.net/avatar/'.$uuid,
        );
    }

    public function toJson(): array
    {
        return [
            'name' => $this->name,
            'url' => $this->url,
            'icon_url' => $this->iconUrl,
        ];
    }
}
