<?php
namespace App\Modules\Discord\Services;

use GuzzleHttp\Client;


class DiscordNotifyService {
    
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client) {
        $this->client = $client;
    }

    /**
     * Sends a message via a Discord webhook
     * @see https://discordapp.com/developers/docs/resources/webhook
     *
     * @param String $name
     * @param String $message
     */
    public function notifyChannel(String $name, String $message) {
        $endpoint = env('DISCORD_WEBHOOK_URL');
        $this->client->post($endpoint, [
            'json' => [
                'username'  => $name,
                'content'   => $message,
            ],
        ]);
    }
}