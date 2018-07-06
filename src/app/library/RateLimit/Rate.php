<?php
namespace App\Library\RateLimit;

class Rate {
    public const MILLISECONDS   = 0.001;
    public const SECONDS        = 1.0;
    public const MINUTES        = 60.0;
    public const HOURS          = 3600.0;

    /**
     * @var float
     */
    private $tokensToRefill;

    /**
     * @var float
     */
    private $interval;

    /**
     * @var float
     */
    private $rate;


    public function getRefillRate() : float {
        return 1 / ($this->interval * $this->rate);
    }

    public function getRefillAmount() : int {
        return $this->tokensToRefill;
    }


    private function __construct(float $tokensToRefill) {
        $this->tokensToRefill = $tokensToRefill;
    }

    /**
     * Number of tokens to refill each time
     *
     * @param float $tokensToRefill
     * @return Rate
     */
    public static function refill(float $tokensToRefill) : Rate {
        return new Rate($tokensToRefill);
    }
    
    /**
     * Specifies the amount of time to
     * wait between refills
     *
     * @param float $interval
     * @param int $rate
     * @return Rate
     */
    public function every(float $interval, int $rate) : Rate {
        $this->interval = $interval;
        $this->rate = $rate;

        return $this;
    }
}