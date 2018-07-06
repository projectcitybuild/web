<?php
namespace App\Modules\Authentication\Services;

use bandwidthThrottle\tokenBucket\storage\SessionStorage;
use bandwidthThrottle\tokenBucket\Rate;
use bandwidthThrottle\tokenBucket\TokenBucket;
use Illuminate\Support\Facades\Session;


class LoginRateLimitService {

    private const SESSION_KEY  = 'login.bucket';

    private const SESSION_NAME = SessionStorage::SESSION_NAMESPACE . self::SESSION_KEY;

    /**
     * @var TokenBucket
     */
    private $bucket;
    
    /**
     * @var boolean
     */
    private $isBootstrapped = false;

    public function bootstrap() {
        $storage = new SessionStorage(self::SESSION_KEY);
        $rate    = new Rate(1, Rate::MINUTE);
        $bucket  = new TokenBucket(6, $rate, $storage);

        // SessionStorage does not seem to preserve
        // any previously remaining tokens if we
        // bootstrap it with a parameter, so we'll
        // have to do the check manually ourselves
        $session = $_SESSION[self::SESSION_NAME];
        if ($session !== null) {
            dd('session exists');
            $bucket->bootstrap();
        } else {
            $bucket->bootstrap(6);
        }

        $this->bucket = $bucket;
        $this->isBootstrapped = true;
    }

    /**
     * Attempts to consume the given number
     * of tokens. Returns false if no tokens
     * are available
     *
     * @param integer $tokens
     * @return boolean
     */
    public function consume(int $tokens = 1) : bool {
        if ($this->isBootstrapped === false) {
            throw new \Exception('LoginRateLimitService has not been bootstrapped');
        }
        return $this->bucket->consume($tokens);
    }

    /**
     * Returns the number of currently
     * available tokens in the bucket
     *
     * @return integer
     */
    public function getRemainingTokens() : int {
        if ($this->isBootstrapped === false) {
            throw new \Exception('LoginRateLimitService has not been bootstrapped');
        }
        return $this->bucket->getTokens();
    }

}