<?php

namespace App\Domains\MinecraftEventBus\UseCases;

use App\Models\Server;
use App\Models\ServerToken;
use Illuminate\Support\Facades\Http;

class PostEventToServer
{
    public function send(
        Server $server,
        string $path,
        array $payload,
    ) {
        $token = $this->getServerToken($server);
        $url = $this->getServerAddress($server) . '/webhook' . $path;

        return Http::withHeader('Authorization', 'Bearer '.$token)
            ->post($url, $payload)
            ->json();
    }

    private function getServerAddress(Server $server): string
    {
        $address = $server->ip;
        if (! empty($server->port)) {
            $address .= ':'.$server->port;
        }
        return $address;
    }

    private function getServerToken(Server $server): ?ServerToken
    {
        return ServerToken::where('server_id', $server->getKey())->firstOrFail();
    }
}
