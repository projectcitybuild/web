<?php

namespace Domain\ServerStatus;

use App\Entities\GameType;
use App\Entities\Servers\Models\Server;
use Domain\ServerStatus\Entities\ServerQueryResult;
use Domain\ServerStatus\Exceptions\UnsupportedGameException;
use ServerStatusRepositoryContract;

final class ServerQueryService
{
    /**
     * Queries the given server address for its current status
     *
     * @param Server $server
     * @param ServerStatusRepositoryContract $serverStatusRepository
     *
     * @return ServerQueryResult
     *
     * @throws UnsupportedGameException
     */
    public function query(Server $server, ServerStatusRepositoryContract $serverStatusRepository): ServerQueryResult
    {
        $time = time();

        $adapter = $this->getQueryAdapter($server->gameType());
        $status = $adapter->query(
            $server->ip,
            $server->port
        );

        $serverStatusRepository->store($status, $time);

        return $status;
    }

    private function getQueryAdapter(GameType $gameType): ServerQueryAdapter
    {
        switch ($gameType) {
            case GameType::Minecraft:
                return new MinecraftQueryAdapter();
            default:
                throw new UnsupportedGameException($gameType->name() . " cannot be queried");
        }
    }
}
