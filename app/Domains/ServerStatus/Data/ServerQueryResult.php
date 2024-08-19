<?php

namespace App\Domains\ServerStatus\Data;

final class ServerQueryResult
{
    public function __construct(
        public bool $isOnline,
        public ?int $numOfPlayers,
        public ?int $numOfSlots,
    ) {
    }

    public static function online(
        int $numOfPlayers,
        int $numOfSlots,
    ): self {
        return new ServerQueryResult(
            isOnline: true,
            numOfPlayers: $numOfPlayers,
            numOfSlots: $numOfSlots,
        );
    }

    public static function offline(): self
    {
        return new ServerQueryResult(
            isOnline: false,
            numOfPlayers: null,
            numOfSlots: null,
        );
    }

    public function toArray(): array
    {
        return [
            'is_online' => $this->isOnline,
            'num_of_players' => $this->numOfPlayers,
            'num_of_slots' => $this->numOfSlots,
        ];
    }
}
