<?php

namespace Library\Discourse\Api;

use GuzzleHttp\Client;
use Library\Discourse\Authentication\DiscoursePayloadValidator;
use Library\Discourse\Entities\DiscoursePayload;
use Library\Discourse\Exceptions\UserNotFound;
use function GuzzleHttp\json_decode;

class DiscourseAdminApi
{
    private Client $client;

    public function __construct(
        private DiscourseClientFactory $clientFactory,
        private DiscoursePayloadValidator $discoursePayloadValidator
    ) {
        $this->client = $this->clientFactory->make();
    }

    /**
     * Returns the user that matches the given email address.
     */
    public function fetchUserByEmail(string $email): array
    {
        $response = $this->client->get('admin/users/list/all.json', [
            'query' => [
                'email' => $email,
            ],
        ]);

        $users = json_decode($response->getBody(), true);

        if (count($users) < 1) {
            throw new UserNotFound();
        }

        return $users[0];
    }

    /**
     * Logs out the given user.
     */
    public function requestLogout(int $discourseUserId): array
    {
        $response = $this->client->post('admin/users/'.$discourseUserId.'/log_out');

        return json_decode($response->getBody(), true);
    }

    /**
     * Finds (or creates) a Discourse account that matches the
     * given payload parameters. If the account exists, it will
     * be updated with the given parameters.
     *
     * @param  DiscoursePayload  $payload
     * @return array
     */
    public function requestSSOSync(array $payload)
    {
        $payload = $this->discoursePayloadValidator->makePayload($payload);
        $signature = $this->discoursePayloadValidator->getSignedPayload($payload);

        $response = $this->client->post('admin/users/sync_sso', [
            'query' => [
                'sso' => $payload,
                'sig' => $signature,
            ],
        ]);

        return json_decode($response->getBody());
    }

    public function fetchUserByDiscourseId(int $discourseId): array
    {
        $response = $this->client->get('admin/users/'.$discourseId.'.json');

        return json_decode($response->getBody(), true);
    }

    public function fetchEmailsByUsername(string $username): array
    {
        $response = $this->client->get('u/'.$username.'/emails.json');

        return json_decode($response->getBody(), true);
    }

    public function addUserToGroup(string $discourseId, int $groupId)
    {
        $this->client->post('admin/users/'.$discourseId.'/groups');
    }

    public function removeUserFromGroup(string $discourseId, int $groupId)
    {
        $this->client->delete('admin/users/'.$discourseId.'/groups/'.$groupId);
    }
}
