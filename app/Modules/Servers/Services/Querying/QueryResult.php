<?php
namespace App\Modules\Servers\Services\Querying;

class QueryResult {

    private $numOfPlayers;
    private $numOfSlots;
    private $playerList;
    private $isOnline;
    private $exception;

    public function __construct(bool $isOnline, int $numOfPlayers, int $numOfSlots, array $playerList = []) {
        $this->isOnline = $isOnline;
        $this->numOfPlayers = $numOfPlayers;
        $this->numOfSlots = $numOfSlots;
        $this->playerList = $playerList;

        if(isset($time)) {
            $this->time = $time;
        }
    }
    
    public function setException(\Exception $exception) {
        $this->exception = $exception;
    }
    public function getException() : \Exception {
        return $this->exception;
    }
    public function hasException() : bool {
        return isset($this->exception);
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