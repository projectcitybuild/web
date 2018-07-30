<?php
namespace Domains\Library\Discourse\Authentication;

use Domains\Library\Discourse\Exceptions\BadSSOPayloadException;
use Illuminate\Log\Logger;


class DiscourseLoginHandler
{
    /**
     * @var DiscourseNonceStorage
     */
    private $storage;

    /**
     * @var DiscoursePayloadValidator
     */
    private $payloadValidator;

    /**
     * @var Logger
     */
    private $logger;

    
    public function __construct(DiscourseNonceStorage $storage,
                                DiscoursePayloadValidator $payloadValidator,
                                Logger $logger)
    {
        $this->storage = $storage;
        $this->payloadValidator = $payloadValidator;
        $this->log = $logger;
    }


    public function verifyPayload(string $sso, string $signature)
    {
        // validate that the given signature matches the
        // payload when signed with our private key. This
        // prevents any payload tampering
        $isValidPayload = $this->payloadValidator->isValidPayload($sso, $signature);
        if ($isValidPayload === false) {
            $this->log->debug('Received invalid SSO payload (sso: '.$sso.' | sig: '.$signature);
            throw new BadSSOPayloadException('Invalid SSO payload');
        }

        // ensure that the payload has all the necessary
        // data required to create a new payload after
        // authentication
        $payload = null;
        try {
            $payload = $this->payloadValidator->unpackPayload($sso);
        } catch (BadSSOPayloadException $e) {
            $this->log->debug('Failed to unpack SSO payload (sso: '.$sso.' | sig: '.$signature);
            throw $e;
        }

        // store the nonce and return url in a session so
        // the user cannot access or tamper with it at any
        // point during authentication
        $this->storage->store($payload['nonce'], $payload['return_sso_url']);
        $this->log->debug('Storing SSO data in session for login');
    }


    public function getLoginRedirectUrl(int $pcbId, string $email)
    {
        $sso = $this->storage->get();

        $nonce     = $sso['nonce'];
        $returnUrl = $sso['return_uri'];

        if ($nonce === null || $returnUrl === null) {
            throw new BadSSOPayloadException('`nonce` or `return` key missing in session');
        }

        $payload = (new DiscoursePayload($nonce))
            ->setPcbId($pcbId)
            ->setEmail($email)
            ->requiresActivation(false)
            ->build();

        // generate new payload to send to discourse
        $payload    = $this->payloadValidator->makePayload($payload);
        $signature  = $this->payloadValidator->getSignedPayload($payload);

        // attach parameters to return url
        $endpoint   = $this->payloadValidator->getRedirectUrl($returnUrl, $payload, $signature);

        $this->storage->clear();

        $this->log->info('Logging in user: '.$pcbId);

        return $endpoint;
    }

    

}