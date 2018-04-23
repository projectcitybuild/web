<?php
namespace App\Modules\Discourse\Services\Api;

use function GuzzleHttp\json_decode;
use App\Modules\Discourse\Services\Authentication\DiscoursePayload;
use App\Modules\Discourse\Services\Authentication\DiscourseAuthService;


class DiscourseAdminApi {

    /**
     * @var DiscourseClient
     */
    private $client;

    /**
     * @var DiscourseAuthService
     */
    private $discourseAuthService;

    public function __construct(DiscourseClient $client, DiscourseAuthService $discourseAuthService) {
        $this->client = $client;
        $this->discourseAuthService = $discourseAuthService;
    }


    private function getApiKey() {
        return env('DISCOURSE_API_KEY');
    }

    private function getApiUser() {
        return 'Andy';
    }

    /**
     * Returns a list of users that match the given email address
     *
     * @param string $email
     * @return array
     */
    public function fetchUsersByEmail(string $email) : array {
        $response = $this->client->get('admin/users/list/all.json', [
            'query' => [
                'api_key'       => $this->getApiKey(),
                'api_username'  => $this->getApiUser(),
                'email'         => $email,
            ],
        ]);
        return json_decode($response, true);
    }

    /**
     * Logs out the given user
     *
     * @param integer $discourseUserId
     * @param string $discourseUsername
     * @return array
     */
    public function requestLogout(int $discourseUserId, string $discourseUsername) : array {
        $response = $this->client->post('admin/users/'.$discourseUserId.'/log_out', [
            'query' => [
                'api_key'       => $this->getApiKey(),
                'api_username'  => $discourseUsername,
            ],
        ]);
        return json_decode($response, true);
    }

    /**
     * Finds (or creates) a Discourse account that matches the
     * given payload parameters. If the account exists, it will 
     * be updated with the given parameters.
     *
     * @param DiscoursePayload $payload
     * @return array
     */
    public function requestSSOSync(DiscoursePayload $payload) : array {
        $payload   = $this->discourseAuthService->makePayload($payload);
        $signature = $this->discourseAuthService->getSignedPayload($payload);

        return [
            'sso'           => $payload,
            'sig'           => $signature,
            'api_key'       => $this->getApiKey(),
            'api_username'  => 'Andy',
        ];

        $response = $client->post('admin/users/sync_sso', [
            'form_params' => $syncPayload,
        ]);

        return json_decode($response);
    }
}