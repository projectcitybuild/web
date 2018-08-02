<?php
namespace Domains\Library\ServerQuery\GameAdapters;

use Domains\Library\ServerQuery\ServerQueryAdapterContract;
use Domains\Library\ServerQuery\ServerQueryResult;

class MockQueryAdapter implements ServerQueryAdapterContract
{
    /**
     * @var bool
     */
    private $isOnline;

    /**
     * @var int
     */
    private $playerCount;

    /**
     * @var int
     */
    private $maxPlayers;

    /**
     * @var array
     */
    private $players;

    
    public function setIsOnline(bool $isOnline)
    {
        $this->isOnline = $isOnline;
    }

    public function setPlayerCount(int $count)
    {
        $this->playerCount = $count;
    }

    public function setMaxPlayers(int $count)
    {
        $this->maxPlayers = $count;
    }

    public function setPlayers(array $players)
    {
        $this->players = $players;
    }

    /**
     * {@inheritDoc}
     */
    public function query(string $ip, $port = null) : ServerQueryResult
    {
        if ($this->isOnline) {
            return new ServerQueryResult(true,
                                        $this->playerCount,
                                        $this->maxPlayers,
                                        $this->players);
        }
        return new ServerQueryResult(false, 0, 0, []);
    }

}
