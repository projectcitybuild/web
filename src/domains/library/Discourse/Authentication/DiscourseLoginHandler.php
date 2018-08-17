<?php
namespace Domains\Library\Discourse\Authentication;

use Domains\Library\Discourse\Api\DiscourseSSOApi;
use Domains\Library\Discourse\Exceptions\BadSSOPayloadException;
use Domains\Library\Discourse\Entities\DiscourseNonce;
use Domains\Library\Discourse\Entities\DiscoursePackedNonce;
use Domains\Library\Discourse\Entities\DiscoursePayload;
use Illuminate\Log\Logger;


class DiscourseLoginHandler
{
    /**
     * @var DiscourseSSOApi
     */
    private $ssoApi;

    /**
     * @var DiscoursePayloadValidator
     */
    private $payloadValidator;

    /**
     * @var Logger
     */
    private $logger;

    
    public function __construct(DiscourseSSOApi $ssoApi,
                                DiscoursePayloadValidator $payloadValidator,
                                Logger $logger)
    {
        $this->ssoApi = $ssoApi;
        $this->payloadValidator = $payloadValidator;
        $this->log = $logger;
    }

    public function getRedirectUrl(int $pcbId, string $email)
    {
        $packedNonce = $this->getPackedNonce();
        $nonce = $this->unpackNoncePayload($packedNonce->getSSO(), 
                                           $packedNonce->getSignature());

        return $this->getDiscourseRedirectUri($pcbId, 
                                              $email, 
                                              $nonce->getNonce(), 
                                              $nonce->getRedirectUri());
    }

    private function getPackedNonce() : DiscoursePackedNonce
    {
        $response = $this->ssoApi->requestNonce();
        $nonce = new DiscoursePackedNonce($response['sso'], $response['sig']);

        return $nonce;
    }

    private function unpackNoncePayload(string $sso, string $signature) : DiscourseNonce
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

        return new DiscourseNonce($payload['nonce'], $payload['return_sso_url']);
    }

    private function getDiscourseRedirectUri(int $pcbId, string $email, string $nonce, string $returnUri)
    {
        $payload = (new DiscoursePayload($nonce))
            ->setPcbId($pcbId)
            ->setEmail($email)
            ->requiresActivation(false)
            ->build();

        // generate new payload to send to discourse
        $payload   = $this->payloadValidator->makePayload($payload);
        $signature = $this->payloadValidator->getSignedPayload($payload);

        // attach parameters to return url
        $endpoint  = $this->payloadValidator->getRedirectUrl($returnUri, $payload, $signature);

        return $endpoint;
    }

    

}