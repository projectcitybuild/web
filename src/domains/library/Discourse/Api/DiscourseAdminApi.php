<?php
namespace Domains\Library\Discourse\Api;

use function GuzzleHttp\json_decode;
use Domains\Library\Discourse\Authentication\DiscoursePayload;
use Domains\Library\Discourse\Authentication\DiscoursePayloadValidator;

class DiscourseAdminApi
{

    /**
     * @var DiscourseClient
     */
    private $client;

    /**
     * @var DiscoursePayloadValidator
     */
    private $discoursePayloadValidator;

    
    public function __construct(DiscourseClient $client,
                                DiscoursePayloadValidator $discoursePayloadValidator) 
    {
        $this->client = $client;
        $this->discoursePayloadValidator = $discoursePayloadValidator;
    }


    private function getApiKey()
    {
        return env('DISCOURSE_API_KEY');
    }

    private function getApiUser()
    {
        return env('DISCOURSE_API_USER');
    }

    /**
     * Returns a list of users that match the given email address
     *
     * @param string $email
     * @return array
     */
    public function fetchUsersByEmail(string $email) : array
    {
        $response = $this->client->get('admin/users/list/all.json', [
            'query' => [
                'api_key'       => $this->getApiKey(),
                'api_username'  => $this->getApiUser(),
                'email'         => $email,
            ],
        ]);
        return json_decode($response->getBody(), true);
    }

    /**
     * Logs out the given user
     *
     * @param integer $discourseUserId
     * @param string $discourseUsername
     * @return array
     */
    public function requestLogout(int $discourseUserId, string $discourseUsername) : array
    {
        $response = $this->client->post('admin/users/'.$discourseUserId.'/log_out', [
            'query' => [
                'api_key'       => $this->getApiKey(),
                'api_username'  => $discourseUsername,
            ],
        ]);
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
                'sig'           => $signature,
                'api_key'       => $this->getApiKey(),
                'api_username'  => $this->getApiUser(),
            ],
        ]);

        return json_decode($response->getBody());
    }

    public function addUserToGroup(int $userId, int $groupId)
    {
        $response = $this->client->post('admin/users/'.$userId.'/groups', [
            'query' => [
                'api_key'       => $this->getApiKey(),
                'api_username'  => $this->getApiUser(),
            ],
            'form_params' => [
                'group_id'      => $groupId,
            ],
        ]);

        return json_decode($response->getBody());
    }

    public function deleteUserFromGroup(int $userId, int $groupId)
    {
        $response = $this->client->delete('admin/users/'.$userId.'/groups/'.$groupId, [
            'query' => [
                'api_key'       => $this->getApiKey(),
                'api_username'  => $this->getApiUser(),
            ],
        ]);

        return json_decode($response->getBody());
    }
}
