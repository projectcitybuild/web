<?php

namespace Domain\ServerStatus\Events;

use Domain\ServerStatus\Entities\ServerQueryResult;
use Entities\Models\GameType;
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
