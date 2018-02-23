<?php
namespace App\Modules\Forums\Services\Authentication;

use App\Modules\Forums\Exceptions\BadPayloadException;


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
     * Returns whether the given query string matches the
     * given signature when signed with our key
     *
     * @param string $queryString
     * @param string $signature
     * 
     * @return boolean
     */
    public function isValidPayload(?string $queryString, ?string $signature) : bool {
        if(empty($queryString) || empty($signature)) {
            return false;
        }

        $payload = urldecode($queryString);
        $signedPayload = $this->signPayload($payload);

        return $signedPayload === $signature;
    }

    private function getNonce(string $queryString) {
        $payload = urldecode($queryString);
        $nonce = base64_decode($payload);

        if(!array_key_exists('nonce', $nonce)) {
            throw new BadPayloadException('bad_nonce', 'Nonce key not present in payload');
        }

        return $nonce['nonce'];
    }

    /**
     * Hashes the given payload with our key
     *
     * @param string $payload
     * 
     * @return string
     */
    private function signPayload(string $payload) : string {
        return hash_hmac('sha256', $payload, $this->key);
    }



}