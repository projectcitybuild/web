<?php

namespace App\Domains\ServerStatus\Adapters;

use App\Domains\ServerStatus\Data\ServerQueryResult;
use Illuminate\Support\Facades\Log;
use xPaw\MinecraftPing;
use xPaw\MinecraftPingException;

final class MinecraftQueryAdapter implements ServerQueryAdapter
{
    public function query(string $ip, $port = 25565): ServerQueryResult
    {
        try {
            $ping = new MinecraftPing($ip, $port);
            $response = $ping->Query();

            Log::debug('Successfully pinged server', ['response' => $response]);

            if ($response === false) {
                return ServerQueryResult::offline();
            }

            $players = $response['players'];

            return ServerQueryResult::online(
                numOfPlayers: $players['online'],
                numOfSlots: $players['max'],
            );
        } catch (MinecraftPingException) {
            return ServerQueryResult::offline();
        }
    }
}
