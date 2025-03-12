<?php

namespace App\Core\Domains\Discord\Data;

class DiscordMessage
{
    /**
     * @param string|null $content The message contents (up to 2000 characters)
     * @param string|null $username Override the default username of the webhook
     * @param string|null $avatarUrl Override the default avatar of the webhook
     * @param string|null $threadName Name of thread to create (requires the webhook channel to be a forum or media channel)
     * @param int|null $timestamp Timestamp for the message
     * @param bool $tts True if this is a text-to-speech message
     * @param array<DiscordEmbed> $embeds Embedded rich content
     *
     * @see https://discord.com/developers/docs/resources/webhook#execute-webhook
     */
    public function __construct(
        public ?string $content = null,
        public ?string $username = null,
        public ?string $avatarUrl = null,
        public ?string $threadName = null,
        public ?int $timestamp = null,
        public bool $tts = false,
        public array $embeds = [],
    ) {}

    public function toJson(): array
    {
        return [
            'content' => $this->content,
            'username' => $this->username,
            'avatar_url' => $this->avatarUrl,
            'thread_name' => $this->threadName,
            'timestamp' => $this->timestamp,
            'tts' => $this->tts,
            'embeds' => array_map(fn ($embed) => $embed->toJson(), $this->embeds),
        ];
    }
}
