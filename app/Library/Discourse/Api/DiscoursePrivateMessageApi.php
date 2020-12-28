<?php

namespace App\Library\Discourse\Api;

class DiscoursePrivateMessageApi extends DiscourseAPIRequest
{
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
