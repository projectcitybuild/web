<?php

namespace App\Library\QueryServer\GameAdapters;

use App\Library\QueryServer\ServerQueryAdapterContract;
use App\Library\QueryServer\ServerQueryResult;

final class MockQueryAdapter implements ServerQueryAdapterContract
{
    /**
     * @var bool
     */
    private $isOnline = false;

    /**
     * @var int
     */
    private $playerCount = 0;

    /**
     * @var int
     */
    private $maxPlayers = 0;

    /**
     * @var array
     */
    private $players = [];

    public function setIsOnline(bool $isOnline): MockQueryAdapter
    {
        $this->isOnline = $isOnline;

        return $this;
    }

    public function setPlayerCount(int $count): MockQueryAdapter
    {
        $this->playerCount = $count;

        return $this;
    }

    public function setMaxPlayers(int $count): MockQueryAdapter
    {
        $this->maxPlayers = $count;

        return $this;
    }

    public function setPlayers(array $players): MockQueryAdapter
    {
        $this->players = $players;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function query(string $ip, $port = null): ServerQueryResult
    {
        if ($this->isOnline) {
            return new ServerQueryResult(
                true,
                $this->playerCount,
                $this->maxPlayers,
                $this->players
            );
        }

        return new ServerQueryResult(false, 0, 0, []);
    }
}
