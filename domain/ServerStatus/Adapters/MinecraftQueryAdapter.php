<?php

namespace Domain\ServerStatus;

use Domain\ServerStatus\Entities\ServerQueryResult;
use xPaw\MinecraftQuery;
use xPaw\MinecraftQueryException;

final class MinecraftQueryAdapter implements ServerQueryAdapter
{
    private MinecraftQuery $queryService;

    public function __construct()
    {
        $this->queryService = MinecraftQuery();
    }

    public function query(string $ip, $port = 25565): ServerQueryResult
    {
        try {
            $this->queryService->Connect($ip, $port);

            $status = $this->queryService->GetInfo() ?: [];
            $onlinePlayerNames = $this->queryService->GetPlayers() ?: [];

            $numPlayers = $status['Players'];
            $numSlots = $status['MaxPlayers'];

            return ServerQueryResult::online(
                $numPlayers,
                $numSlots,
                $onlinePlayerNames
            );
        } catch (MinecraftQueryException $e) {
            return ServerQueryResult::offline();
        }
    }
}
