<?php
namespace App\Modules\Servers\Services\Querying\GameAdapters;

use App\Modules\Servers\Services\Querying\{QueryAdapterInterface, QueryResult};

/**
 * Server query adapter for integration testing
 */
class MockQueryAdapter implements QueryAdapterInterface {

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

    public function __construct(
        bool $isOnline, 
        int $playerCount = 0, 
        int $maxPlayers = 0, 
        array $players = []
    ) {
        $this->isOnline = $isOnline;
        $this->playerCount = $playerCount;
        $this->maxPlayers = $maxPlayers;
        $this->players = $players;
    }

    /**
     * {@inheritDoc}
     */
    public function query(string $ip, $port = null) : QueryResult {
        if($this->isOnline) {
            return new QueryResult(
                true,
                $this->playerCount,
                $this->maxPlayers,
                $this->players
            );
        }
        return new QueryResult(false, 0, 0, []);
    }

}