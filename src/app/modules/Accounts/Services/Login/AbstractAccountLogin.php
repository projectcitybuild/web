<?php
namespace App\Modules\Accounts\Services\Login;

use App\Modules\Discourse\Services\Authentication\DiscoursePayload;
use App\Modules\Discourse\Services\Authentication\DiscourseAuthService;
use App\Modules\Accounts\Exceptions\InvalidDiscoursePayloadException;
use Illuminate\Http\Request;


abstract class AbstractAccountLogin {

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $nonce;

    /**
     * @var string
     */
    protected $returnUrl;

    /**
     * @var DiscourseAuthService
     */
    private $discourseAuthService;


    abstract protected function execute(string $nonce, string $returnUrl);

    
    public function __construct(DiscourseAuthService $discourseAuthService) {
        $this->discourseAuthService = $discourseAuthService;
    }

    public function login(Request $request) {
        $this->request = $request;
        $this->getInitialPayload();

        return $this->execute($this->nonce, $this->returnUrl);
    }

    protected function getInitialPayload() {
        $session = $this->request->session();

        $this->nonce     = $session->get('discourse_nonce');
        $this->returnUrl = $session->get('discourse_return');

        if($this->nonce === null || $this->returnUrl === null) {
            throw new InvalidDiscoursePayloadException('`nonce` or `return` key missing in session');
        }
    }

    protected function invalidateSessionData() {
        $session = $this->request->session();
    
        $session->remove('discourse_nonce');
        $session->remove('discourse_return');        
    }

    protected function redirectToEndpoint(DiscoursePayload $payloadBuilder) {
        $payload = $payloadBuilder->build();
    
        // generate new payload to send to discourse
        $payload    = $this->discourseAuthService->makePayload($payload);
        $signature  = $this->discourseAuthService->getSignedPayload($payload);

        // attach parameters to return url
        $endpoint   = $this->discourseAuthService->getRedirectUrl($this->returnUrl, $payload, $signature);

        return redirect()->to($endpoint);
    }

}