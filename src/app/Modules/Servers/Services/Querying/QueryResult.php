<?php
namespace App\Modules\Servers\Services\Querying;

/**
 * Represents data returned from querying a server
 */
class QueryResult {

    /**
     * @var int
     */
    private $numOfPlayers;

    /**
     * @var int
     */
    private $numOfSlots;

    /**
     * @var array
     */
    private $playerList;

    /**
     * @var bool
     */
    private $isOnline;

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
    
    public function getPlayerList() : array {
        if(!$this->isOnline()) { 
            return [];
        }
        return $this->playerList;
    }
    
    public function getNumOfPlayers() : int {
        if(!$this->isOnline()) {
            return 0;
        }
        return $this->numOfPlayers;
    }

    public function getNumOfSlots() : int {
        return $this->numOfSlots;
    }

    public function isOnline() : bool {
        return $this->isOnline;
    }

}