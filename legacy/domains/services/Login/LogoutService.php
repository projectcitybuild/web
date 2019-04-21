<?php
namespace Domains\Services\Login;

use Illuminate\Contracts\Auth\Guard as Auth;
use Domains\Library\Discourse\Api\DiscourseUserApi;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Log\Logger;
use Domains\Library\Discourse\Api\DiscourseAdminApi;

class LogoutService 
{

    /**
     * @var DiscourseUserApi
     */
    private $discourseUserApi;

    /**
     * @var DiscourseAdminApi
     */
    private $discourseAdminApi;

    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var Logger
     */
    private $log;

    
    public function __construct(DiscourseUserApi $discourseUserApi,
                                DiscourseAdminApi $discourseAdminApi,
                                Auth $auth,
                                Logger $logger)
    {
        $this->discourseUserApi = $discourseUserApi;
        $this->discourseAdminApi = $discourseAdminApi;
        $this->auth = $auth;
        $this->log = $logger;
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

        $this->log->info('Logging out user: '.$pcbId);

        try {
            $this->discourseAdminApi->requestLogout($user['id']);

        } catch (ClientException $error) {
            // Discourse will throw a 404 error if the
            // user attempts to logout when not logged-in.
            // When that happens, we want to gracefully
            // logout of just PCB
            if ($error->getCode() !== '404') {
                $this->log->notice('Caught a 404 error logging out of Discourse');
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
        $result = $this->discourseUserApi->fetchUserByPcbId($pcbId);

        $user = $result['user'];
        if ($user === null) {
            throw new \Exception('Discourse logout api response did not have a `user` key');
        }
        
        $this->log->debug('Fetched Discord user', [
            'id'        => $user['id'],
            'username'  => $user['username'],
            'response'  => $result,
        ]);
        
        return $user;
    }

}