<?php

namespace App\Domains\ServerStatus\Events;

use App\Core\Data\GameType;
use App\Domains\ServerStatus\Entities\ServerQueryResult;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ServerStatusFetched
{
    use Dispatchable, SerializesModels;

    public ServerQueryResult $result;
    public GameType $gameType;
    public int $fetchTimestamp;

    public function __construct(
        ServerQueryResult $result,
        GameType $gameType,
        int $fetchTimestamp
    ) {
        $this->result = $result;
        $this->gameType = $gameType;
        $this->fetchTimestamp = $fetchTimestamp;
    }
}
