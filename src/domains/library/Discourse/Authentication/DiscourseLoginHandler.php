<?php
namespace Domains\Library\Discourse\Authentication;

use Domains\Library\Discourse\Api\DiscourseSSOApi;
use Domains\Library\Discourse\Exceptions\BadSSOPayloadException;
use Domains\Library\Discourse\Entities\DiscourseNonce;
use Domains\Library\Discourse\Entities\DiscoursePayload;

final class DiscourseLoginHandler
{
    /**
     * @var DiscourseSSOApi
     */
    private $ssoApi;

    /**
     * @var DiscoursePayloadValidator
     */
    private $payloadValidator;


    public function __construct(
        DiscourseSSOApi $ssoApi,
        DiscoursePayloadValidator $payloadValidator
    ) {
        $this->ssoApi = $ssoApi;
        $this->payloadValidator = $payloadValidator;
    }

    public function getRedirectUrl(int $pcbId, string $email)
    {
        $packedNonce = $this->ssoApi->requestPackedNonce();

        $nonce = $this->decodePackedNonce(
            $packedNonce->getSSO(), 
            $packedNonce->getSignature()
        );
        return $this->getDiscourseRedirectUri(
            $pcbId, 
            $email, 
            $nonce->getNonce(), 
            $nonce->getRedirectUri()
        );
    }
    
    /**
     * Decodes the given packed nonce, and extracts the nonce and
     * return URL from it
     *
     * @param string $sso
     * @param string $signature
     * @return DiscourseNonce
     */
    private function decodePackedNonce(string $sso, string $signature) : DiscourseNonce
    {
        // validate that the given signature matches the payload when 
        // signed with our private key. This prevents any payload tampering
        $isValidPayload = $this->payloadValidator->isValidPayload($sso, $signature);

        if ($isValidPayload === false) 
        {
            $this->log->debug('Received invalid SSO payload', ['sso' => $sso, 'signature' => $signature]);
            throw new BadSSOPayloadException('Invalid SSO payload');
        }

        // ensure that the payload has all the necessary data required to 
        // create a new payload after authentication
        $payload = null;
        try 
        {
            $payload = $this->payloadValidator->unpackPayload($sso);
        } 
        catch (BadSSOPayloadException $e) 
        {
            $this->log->debug('Failed to unpack SSO payload', ['sso' =>  $sso, 'signature' => $signature]);
            throw $e;
        }

        return new DiscourseNonce($payload['nonce'], $payload['return_sso_url']);
    }

    /**
     * Generates an URL to redirect the user to.
     * 
     * The URL contains a signed payload containing their pcbId, email address,
     * nonce and a URL to redirect to if the login succeeds
     *
     * @param integer $pcbId
     * @param string $email
     * @param string $nonce
     * @param string $returnUri
     * @return string
     */
    private function getDiscourseRedirectUri(int $pcbId, string $email, string $nonce, string $returnUri) : string
    {
        $rawPayload = (new DiscoursePayload($nonce))
            ->setPcbId($pcbId)
            ->setEmail($email)
            ->requiresActivation(false)
            ->build();

        // generate new payload to send to discourse
        $rawPayload = $this->payloadValidator->makePayload($rawPayload);
        $signature  = $this->payloadValidator->getSignedPayload($rawPayload);

        // attach parameters to return url
        $endpoint   = $this->payloadValidator->getRedirectUrl($returnUri, $rawPayload, $signature);

        return $endpoint;
    }

    

}