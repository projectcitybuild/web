<?php

namespace Library\Discourse\Api;

use GuzzleHttp\Client;
use function now;

class DiscoursePrivateMessageApi
{
    private Client $client;

    public function __construct(private DiscourseClientFactory $clientFactory)
    {
        $this->client = $this->clientFactory->make();
    }

    public function createPrivateMessage(string $title, string $message, array $recipients, ?int $createdAt = null): array
    {
        $response = $this->client->post('posts.json', [
            'json' => [
                'title' => $title,
                'raw' => $message,
                'target_usernames' => $recipients,
                'archetype' => 'private_message',
                'created_at' => $createdAt ? $createdAt : now(),
            ],
        ]);

        return json_decode($response->getBody(), true);
    }
}
