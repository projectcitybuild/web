<?php
namespace App\Modules\Discord\Services;

use GuzzleHttp\Client;


class DiscordWebhookService {
    
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client) {
        $this->client = $client;
    }

    public function notifyChannel(String $name, String $message) {
        $endpoint = env('DISCORD_WEBHOOK_URL');
        $this->client->postAsync($endpoint, [
            'json' => [
                'username'  => $name,
                'content'   => $message,
            ],
        ]);
    }
}