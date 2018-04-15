<?php
namespace App\Modules\Discourse\Services\Authentication;

use App\Modules\Forums\Exceptions\BadPayloadException;
use App\Modules\Forums\Exceptions\BadSSOPayloadException;


class DiscourseAuthService {

    /**
     * Key to use when signing a payload
     * 
     * @var string
     */
    private $key;

    public function __construct(string $key = null) {
        if($key === null) {
            throw new \Exception('No Discourse SSO key set');
        }
        $this->key = $key;
    }

    /**
     * Hashes the given string with our private sso secret
     *
     * @param string $payload
     * @return string
     */
    public function getSignedPayload(string $payload) : string {
        return hash_hmac('sha256', $payload, $this->key);
    }

    /**
     * Returns whether the given signature matches the given
     * payload hashed with our private sso secret
     *
     * @param string $payload
     * @param string $signature
     * @return boolean
     */
    public function isValidPayload(string $payload, string $signature) : bool {
        return $this->getSignedPayload($payload) === $signature;
    }

    /**
     * Decodes the given sso string and returns the keys
     * required for creating a new payload after authentication
     *
     * @param string $sso
     * 
     * @throws BadSSOPayloadException
     * @return array
     */
    public function unpackPayload(string $sso) : array {
        $payload = base64_decode($sso);
        $payload = urldecode($payload);

        $discourse = [];
        parse_str($payload, $discourse);

        if(
            array_key_exists('nonce', $discourse) === false || 
            array_key_exists('return_sso_url', $discourse) === false
        ) {
            throw BadSSOPayloadException('nonce or return_sso_url key missing in payload');
        }

        return $discourse;
    }

    /**
     * Encodes the given array into a string that
     * Discourse can handle
     *
     * @param array $data
     * @return string
     */
    public function makePayload(array $data) : string {
        $payload = http_build_query($data);
        $payload = base64_encode($payload);
        $payload = urlencode($payload);

        return $payload;
    }

    /**
     * Attaches the given sso and signature to a return url
     *
     * @param string $url
     * @param string $sso
     * @param string $signature
     * @return string
     */
    public function getRedirectUrl(string $return, string $sso, string $signature) : string {
        return $return.'?'.http_build_query([
            'sso' => $sso,
            'sig' => $signature
        ]);
    }

}