<?php

namespace Domain\ServerStatus\Adapters;

use Domain\ServerStatus\Entities\ServerQueryResult;
use Domain\ServerStatus\ServerQueryAdapter;
use xPaw\MinecraftPing;
use xPaw\MinecraftPingException;
use xPaw\MinecraftQuery;
use xPaw\MinecraftQueryException;

final class MinecraftQueryAdapter implements ServerQueryAdapter
{
    public function query(string $ip, $port = 25565): ServerQueryResult
    {
        try {
            $ping = new MinecraftPing($ip, $port);
            $response = $ping->Query();

            $players = $response['players'];

            return ServerQueryResult::online(
                numOfPlayers: $players['online'],
                numOfSlots: $players['max'],
                onlinePlayerNames: [], // TODO: restore this later
            );
        } catch (MinecraftPingException $e) {
            return ServerQueryResult::offline();
        }
    }
}
