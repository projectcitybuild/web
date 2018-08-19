<?php
namespace Domains\Library\Discourse\Api;

class DiscoursePrivateMessageApi
{
    /**
     * @var DiscourseClient
     */
    private $client;

    public function __construct(DiscourseClient $client)
    {
        $this->client = $client;
    }

    public function createPrivateMessage(string $title, string $message, array $recipients, ?int $createdAt = null) : array
    {
        $response = $this->client->post('posts.json', [
            'json' => [
                'title' => $title,
                'raw' => $message,
                'target_usernames' => $recipients,
                'archetype' => 'private_message',
                'created_at' => $createdAt ?: now(),
            ],
        ]);
        $result = json_decode($response->getBody(), true);

        return $result;
    }
}
