<?php
namespace App\Library\Discourse\Authentication;

use App\Library\Discourse\Api\DiscourseSSOApi;
use App\Library\Discourse\Exceptions\BadSSOPayloadException;
use App\Library\Discourse\Entities\DiscourseNonce;
use App\Library\Discourse\Entities\DiscoursePayload;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Library\Discourse\Entities\DiscoursePackedNonce;

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

    public function getRedirectUrl(Request $request, int $pcbId, string $email)
    {
        $packedNonce = $this->getPackedNonce($request);

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

    private function getPackedNonce(Request $request) : DiscoursePackedNonce
    {
        // if the user pressed the login button from the Discourse side or attempted an authenticated action
        // (eg. replying), Discourse will automatically redirect the user to our login page with an SSO and 
        // Signature parameter in the URL
        if ($request->has('sso') && $request->has('sig'))
        {
            return new DiscoursePackedNonce(
                $request->get('sso'), 
                $request->get('sig')
            );
        }

        // if the SSO and Signature parameters aren't present, it means the user has tapped the login button from
        // the PCB side. In this situation, we'll hit the Discourse SSO URL on the user's behalf to retrieve the
        // SSO and Signature parameter, so the user doesn't have to go through a double redirect (PCB -> Forum -> PCB)
        return $this->ssoApi->requestPackedNonce();
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
            Log::debug('Received invalid SSO payload', ['sso' => $sso, 'signature' => $signature]);
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
            Log::debug('Failed to unpack SSO payload', ['sso' =>  $sso, 'signature' => $signature]);
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