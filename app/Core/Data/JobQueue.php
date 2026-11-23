<?php

namespace App\Core\Data;

enum JobQueue: string
{
    case DEFAULT = 'default';
    case MAIL = 'mail';
    case PLAYER_MAIL = 'player-mail';
    case DISCORD = 'discord-message';

    public function label(): string
    {
        return match ($this) {
            self::DEFAULT => 'Default',
            self::MAIL => 'Mail',
            self::PLAYER_MAIL => 'Player mail',
            self::DISCORD => 'Discord messages',
        };
    }

    public static function normalize(string $queue): string
    {
        while (str_starts_with($queue, 'queues:') || str_starts_with($queue, 'queue:')) {
            $queue = substr($queue, str_starts_with($queue, 'queues:') ? 7 : 6);
        }

        return $queue;
    }

    public static function labelFor(?string $queue): string
    {
        if ($queue === null) {
            return self::DEFAULT->label();
        }

        $queue = self::normalize($queue);

        return self::tryFrom($queue)?->label() ?? $queue;
    }
}
