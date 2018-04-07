<?php
namespace App\Modules\Accounts\Services\Login;

use App\Modules\Discourse\Services\Authentication\DiscoursePayload;
use Illuminate\Http\Request;
use App\Modules\Accounts\Execeptions\InvalidDiscoursePayloadException;
use App\Modules\Discourse\Services\Authentication\DiscourseAuthService;


class AccountLoginService {

    /**
     * @var AccountLoginable
     */
    private $loginExecutor;

    /**
     * @var DiscourseAuthService
     */
    private $discourseAuthService;

    
    public function __construct(DiscourseAuthService $discourseAuthService) {
        $this->discourseAuthService = $discourseAuthService;
    }

    public function setExecutor(AccountLoginable $executor) : AccountLoginService {
        $this->loginExecutor = $executor;
        return $this;
    }

    /**
     * Attempts to login using a pre-set login executors
     *
     * @param string $nonce     Nonce received from Discourse
     * @param string $return    Discourse forum base URL
     * @return string           Endpoint URL to redirect to
     */
    public function login(string $nonce, string $return) : string {
        if($this->loginExecutor === null) {
            throw new \Exception('Login executor not set');
        }

        $payload = $this->loginExecutor
            ->execute($nonce)
            ->build();

        // generate new payload to send to discourse
        $payload    = $this->discourseAuthService->makePayload($payloadBuilder->build());
        $signature  = $this->discourseAuthService->getSignedPayload($payload);

        // attach parameters to return url
        $endpoint   = $this->discourseAuthService->getRedirectUrl($return, $payload, $signature);

        return $endpoint;
    }

}