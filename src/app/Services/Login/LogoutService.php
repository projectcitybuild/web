<?php
namespace App\Services\Login;

use App\Entities\Requests\Discourse\DiscourseUserFetchAPIRequest;
use App\Library\APIClient\APIClient;
use Illuminate\Contracts\Auth\Guard as Auth;
use GuzzleHttp\Exception\ClientException;
use App\Library\Discourse\Api\DiscourseAdminApi;
use Illuminate\Support\Facades\Log;

final class LogoutService 
{
    /**
     * @var APIClient
     */
    private $apiClient;

    /**
     * @var DiscourseAdminApi
     */
    private $discourseAdminApi;

    /**
     * @var Auth
     */
    private $auth;

    
    public function __construct(
        APIClient $apiClient,
        DiscourseAdminApi $discourseAdminApi,
        Auth $auth
    ) {
        $this->apiClient = $apiClient;
        $this->discourseAdminApi = $discourseAdminApi;
        $this->auth = $auth;
    }

    /**
     * Invalidates only a PCB session
     * (used by Discourse)
     *
     * @return boolean
     */
    public function logoutOfPCB() : bool
    {
        if (!$this->auth->check()) {
            return false;
        }
        $this->auth->logout();

        return true;
    }

    /**
     * Invalidates both PCB and Discourse's session
     * (used by PCB)
     *
     * @return boolean
     */
    public function logoutOfDiscourseAndPCB() : bool
    {
        $pcbId = $this->auth->id();

        if ($this->logoutOfPCB() === false) {
            return false;
        }

        $user = $this->getDiscourseUser($pcbId);

        Log::info('Logging out user: '.$pcbId);

        try {
            $this->discourseAdminApi->requestLogout($user['id']);

        } catch (ClientException $error) {
            // Discourse will throw a 404 error if the user attempts to logout but isn't 
            // currently logged-in. If that happens, we'll just gracefully logout of PCB
            if ($error->getCode() !== 404) {
                Log::notice('Caught a 404 error logging out of Discourse');
                throw $error;
            }
        }

        return true;
    }

    /**
     * Fetches the Discourse user associated
     * with the given PCB account ID
     *
     * @param integer $pcbId
     * @return array
     */
    private function getDiscourseUser(int $pcbId) : array
    {
        $request = new DiscourseUserFetchAPIRequest($pcbId);
        $result = $this->apiClient->request($request);

        $user = $result['user'];
        if ($user === null) {
            throw new \Exception('Discourse logout api response did not have a `user` key');
        }
        
        Log::debug('Fetched Discord user', [
            'id'        => $user['id'],
            'username'  => $user['username'],
            'response'  => $result,
        ]);
        
        return $user;
    }

}