<?php

namespace Domain\ServerStatus\Entities;

final class ServerQueryResult
{
    public bool $isOnline;
    public ?int $numOfPlayers;
    public ?int $numOfSlots;
    public ?array $onlinePlayerNames;

    public function __construct(
        bool $isOnline,
        ?int $numOfPlayers,
        ?int $numOfSlots,
        ?array $onlinePlayerNames
    ) {
        $this->isOnline = $isOnline;
        $this->numOfPlayers = $isOnline ? $numOfPlayers : -1;
        $this->numOfSlots = $numOfSlots;
        $this->onlinePlayerNames = $onlinePlayerNames;
    }

    public static function online(
        int $numOfPlayers,
        int $numOfSlots,
        array $onlinePlayerNames
    ): self {
        return new ServerQueryResult(
            true,
            $numOfPlayers,
            $numOfSlots,
            $onlinePlayerNames
        );
    }

    public static function offline(): self
    {
        return new ServerQueryResult(
            false,
            null,
            null,
            null
        );
    }
}
