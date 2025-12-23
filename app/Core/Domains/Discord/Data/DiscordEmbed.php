<?php

namespace App\Core\Domains\Discord\Data;

class DiscordEmbed
{
    /**
     * @param  string|null  $title  Title of embed
     * @param  string|null  $description  Description of embed
     * @param  string|null  $url  Url of embed
     * @param  int|null  $timestamp  Timestamp of embed content
     * @param  string|null  $color  Color code of the embed
     * @param  DiscordAuthor|null  $author  Author information
     * @param  array<DiscordEmbedField>  $fields  Fields information, max of 25
     *
     * @see https://discord.com/developers/docs/resources/message#embed-object
     */
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $url = null,
        public ?int $timestamp = null,
        public ?string $color = null,
        public ?DiscordAuthor $author = null,
        public array $fields = [],
    ) {}

    public function toJson(): array
    {
        return [
            'title' => $this->title,
            'type' => 'rich',
            'description' => $this->description,
            'url' => $this->url,
            'timestamp' => $this->timestamp,
            'color' => ! empty($this->color) ? hexdec($this->color) : null,
            'author' => $this->author?->toJson(),
            'fields' => array_map(fn ($field) => $field->toJson(), $this->fields),
        ];
    }
}
