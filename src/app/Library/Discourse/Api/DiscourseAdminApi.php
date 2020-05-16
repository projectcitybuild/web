<?php

namespace App\Library\Discourse\Api;

use App\Library\Discourse\Exceptions\UserNotFound;
use function GuzzleHttp\json_decode;
use App\Library\Discourse\Entities\DiscoursePayload;
use App\Library\Discourse\Authentication\DiscoursePayloadValidator;

class DiscourseAdminApi extends DiscourseAPIRequest
{
    /**
     * @var DiscoursePayloadValidator
     */
    private $discoursePayloadValidator;


    public function __construct(
        DiscourseClient $client,
        DiscoursePayloadValidator $discoursePayloadValidator
    ) {
        parent::__construct($client);

        $this->discoursePayloadValidator = $discoursePayloadValidator;
    }


    /**
     * Returns the user that matches the given email address
     *
     * @param string $email
     * @return array
     */
    public function fetchUserByEmail(string $email) : array
    {
        $response = $this->client->get('admin/users/list/all.json', [
            'query' => [
                'email'         => $email,
            ],
        ]);

        $users = json_decode($response->getBody(), true);

        if (count($users) < 1) {
            throw new UserNotFound();
        }

        return $users[0];
    }

    /**
     * Logs out the given user
     *
     * @param integer $discourseUserId
     * @return array
     */
    public function requestLogout(int $discourseUserId) : array
    {
        $response = $this->client->post('admin/users/'.$discourseUserId.'/log_out');
        return json_decode($response->getBody(), true);
    }

    /**
     * Finds (or creates) a Discourse account that matches the
     * given payload parameters. If the account exists, it will
     * be updated with the given parameters.
     *
     * @param DiscoursePayload $payload
     * @return array
     */
    public function requestSSOSync(array $payload)
    {
        $payload   = $this->discoursePayloadValidator->makePayload($payload);
        $signature = $this->discoursePayloadValidator->getSignedPayload($payload);

        $response = $this->client->post('admin/users/sync_sso', [
            'query' => [
                'sso'           => $payload,
                'sig'           => $signature
            ],
        ]);

        return json_decode($response->getBody());
    }

    public function fetchUserByDiscourseId(int $discourseId) : array
    {
        $response = $this->client->get('admin/users/'.$discourseId.'.json');
        $result = json_decode($response->getBody(), true);

        return $result;
    }

    public function fetchEmailsByUsername(string $username) : array
    {
        $response = $this->client->get('u/'.$username.'/emails.json');
        $result = json_decode($response->getBody(), true);

        return $result;
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
