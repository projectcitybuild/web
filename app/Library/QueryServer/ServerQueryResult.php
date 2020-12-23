<?php

namespace App\Library\QueryServer;

final class ServerQueryResult
{
    private int $numOfPlayers;

    private int $numOfSlots;

    /**
     * @var array
     */
    private array $playerList;

    private bool $isOnline;

    public function __construct(
        bool $isOnline = false,
        int $numOfPlayers = 0,
        int $numOfSlots = 0,
        array $playerList = []
    ) {
        $this->isOnline = $isOnline;
        $this->numOfPlayers = $numOfPlayers;
        $this->numOfSlots = $numOfSlots;
        $this->playerList = $playerList;
    }

    public function getPlayerList(): array
    {
        if (! $this->isOnline()) {
            return [];
        }
        return $this->playerList;
    }

    public function getNumOfPlayers(): int
    {
        if (! $this->isOnline()) {
            return 0;
        }
        return $this->numOfPlayers;
    }

    public function getNumOfSlots(): int
    {
        return $this->numOfSlots;
    }

    public function isOnline(): bool
    {
        return $this->isOnline;
    }
}
