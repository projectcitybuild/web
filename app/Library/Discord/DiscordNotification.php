<?php

namespace App\Library\Discord;

use GuzzleHttp\Client;

final class DiscordNotification
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Sends a message via a Discord webhook
     *
     * @see https://discordapp.com/developers/docs/resources/webhook
     *
     * @param String $name      Discord channel to send to
     * @param String $message   Message to send
     */
    public function sendToChannel(string $name, string $message)
    {
        $endpoint = config('services.discord.webhook_url');

        $this->client->post($endpoint, [
            'json' => [
                'username' => $name,
                'content' => $message,
            ],
        ]);
    }
}
