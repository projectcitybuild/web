<?php

namespace Library\Discourse\Authentication;

use Library\Discourse\Exceptions\BadSSOPayloadException;
use function config;

final class DiscoursePayloadValidator
{
    /**
     * Key to use when signing a payload.
     *
     * @var string
     */
    private $key;

    public function __construct(?string $key = null)
    {
        if ($key === null) {
            throw new \Exception('No Discourse SSO key set');
        }
        $this->key = $key;
    }

    /**
     * Disables payload checking for the current
     * request.
     */
    public static function disablePayloadChecks()
    {
        config(['discourse.signing_enabled' => false]);
    }

    /**
     * Hashes the given string with our private sso secret.
     */
    public function getSignedPayload(string $payload): string
    {
        return hash_hmac('sha256', $payload, $this->key);
    }

    /**
     * Returns whether the given signature matches the given
     * payload hashed with our private sso secret.
     */
    public function isValidPayload(string $payload, string $signature): bool
    {
        if (config('discourse.signing_enabled', false) === false) {
            return true;
        }

        return $this->getSignedPayload($payload) === $signature;
    }

    /**
     * Decodes the given sso string and returns the keys
     * required for creating a new payload after authentication.
     *
     *
     * @throws BadSSOPayloadException
     */
    public function unpackPayload(string $sso): array
    {
        $payload = base64_decode($sso);
        $payload = urldecode($payload);

        $payloadParameters = [];
        parse_str($payload, $payloadParameters);

        if (array_key_exists('nonce', $payloadParameters) === false ||
            array_key_exists('return_sso_url', $payloadParameters) === false) {
            throw new BadSSOPayloadException('nonce or return_sso_url key missing in payload');
        }

        return $payloadParameters;
    }

    /**
     * Encodes the given array into a string that
     * Discourse can handle.
     */
    public function makePayload(array $data): string
    {
        $payload = http_build_query($data);

        return base64_encode($payload);
    }

    /**
     * Attaches the given sso and signature to a return url.
     */
    public function getRedirectUrl(string $return, string $sso, string $signature): string
    {
        return $return.'?'.http_build_query([
            'sso' => $sso,
            'sig' => $signature,
        ]);
    }
}
