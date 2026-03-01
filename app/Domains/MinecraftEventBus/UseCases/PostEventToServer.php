<?php

namespace App\Domains\MinecraftEventBus\UseCases;

use App\Models\Server;
use App\Models\ServerToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PostEventToServer
{
    public function send(
        Server $server,
        string $path,
        array $payload,
    ): void {
        $token = $this->getServerToken($server);
        $url = $this->getServerAddress($server).'/'.$path;

        Log::info('Sending event to Minecraft server', [
            'server' => $server,
            'url' => $path,
            'payload' => $payload,
        ]);

        $response = Http::withToken($token->token)
            ->acceptJson()
            ->post($url, $payload);

        Log::debug('Received response', ['response' => $response]);
    }

    private function getServerAddress(Server $server): string
    {
        $address = $server->ip;
        if (! empty($server->web_port)) {
            $address .= ':'.$server->web_port;
        }
        return $address;
    }

    private function getServerToken(Server $server): ?ServerToken
    {
        return ServerToken::where('server_id', $server->id)->firstOrFail();
    }
}
