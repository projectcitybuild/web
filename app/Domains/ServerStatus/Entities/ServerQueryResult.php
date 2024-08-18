<?php

namespace App\Domains\ServerStatus\Entities;

final class ServerQueryResult
{
    public function __construct(
        public bool $isOnline,
        public ?int $numOfPlayers,
        public ?int $numOfSlots,
        public array $onlinePlayerNames,
    ) {
    }

    public static function online(
        int $numOfPlayers,
        int $numOfSlots,
        array $onlinePlayerNames,
    ): self {
        return new ServerQueryResult(
            isOnline: true,
            numOfPlayers: $numOfPlayers,
            numOfSlots: $numOfSlots,
            onlinePlayerNames: $onlinePlayerNames,
        );
    }

    public static function offline(): self
    {
        return new ServerQueryResult(
            isOnline: false,
            numOfPlayers: null,
            numOfSlots: null,
            onlinePlayerNames: [],
        );
    }

    public function toArray(): array
    {
        return [
            'is_online' => $this->isOnline,
            'num_of_players' => $this->numOfPlayers,
            'num_of_slots' => $this->numOfSlots,
            'online_player_names' => $this->onlinePlayerNames,
        ];
    }
}
