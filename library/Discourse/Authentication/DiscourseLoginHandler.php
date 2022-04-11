<?php

namespace Library\Discourse\Authentication;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Library\Discourse\Api\DiscourseSSOApi;
use Library\Discourse\Entities\DecodedDiscourseNonce;
use Library\Discourse\Entities\DiscoursePackedNonce;
use Library\Discourse\Entities\DiscoursePayload;
use Library\Discourse\Exceptions\BadSSOPayloadException;

final class DiscourseLoginHandler
{
    public function __construct(
        private DiscoursePayloadValidator $payloadValidator
    ) {}

    /**
     * @throws BadSSOPayloadException
     */
    public function getRedirectUrl(
        DiscoursePackedNonce $packedNonce,
        int $pcbId,
        string $email,
        string $username,
        string $groups,
    ) {
        $nonce = $this->decodePackedNonce($packedNonce);

        return $this->getDiscourseRedirectUri(
            $pcbId,
            $email,
            $username,
            $groups,
            $nonce->getNonce(),
            $nonce->getRedirectUri()
        );
    }

    /**
     * Decodes the given packed nonce, and extracts the nonce and
     * return URL from it.
     */
    private function decodePackedNonce(DiscoursePackedNonce $packedNonce): DecodedDiscourseNonce
    {
        $sso = $packedNonce->sso;
        $signature = $packedNonce->signature;

        // validate that the given signature matches the payload when
        // signed with our private key. This prevents any payload tampering
        $isValidPayload = $this->payloadValidator->isValidPayload($sso, $signature);

        if ($isValidPayload === false) {
            Log::debug('Received invalid SSO payload', ['sso' => $sso, 'signature' => $signature]);
            throw new BadSSOPayloadException('Invalid SSO payload');
        }

        // ensure that the payload has all the necessary data required to
        // create a new payload after authentication
        try {
            $payload = $this->payloadValidator->unpackPayload($sso);
        } catch (BadSSOPayloadException $e) {
            Log::debug('Failed to unpack SSO payload', ['sso' => $sso, 'signature' => $signature]);
            throw $e;
        }

        return new DecodedDiscourseNonce($payload['nonce'], $payload['return_sso_url']);
    }

    /**
     * Generates an URL to redirect the user to.
     *
     * The URL contains a signed payload containing their pcbId, email address,
     * nonce and a URL to redirect to if the login succeeds
     */
    private function getDiscourseRedirectUri(
        int $pcbId,
        string $email,
        string $username,
        string $groups,
        string $nonce,
        string $returnUri,
    ): string {
        $rawPayload = (new DiscoursePayload($nonce))
            ->setPcbId($pcbId)
            ->setEmail($email)
            ->setUsername($username)
            ->setGroups($groups)
            ->requiresActivation(false)
            ->build();

        // generate new payload to send to discourse
        $rawPayload = $this->payloadValidator->makePayload($rawPayload);
        $signature = $this->payloadValidator->getSignedPayload($rawPayload);

        // attach parameters to return url
        return $this->payloadValidator->getRedirectUrl($returnUri, $rawPayload, $signature);
    }
}
