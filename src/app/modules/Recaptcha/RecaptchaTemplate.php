<?php
namespace App\Modules\Recaptcha;

class RecaptchaTemplate {

    private function __construct() {}

    /**
     * Returns this site's public reCAPTCHA key
     *
     * @throws \Exception
     * @return string
     */
    public static function getSiteKey() : string {
        $key = env('RECAPTCHA_SITE_KEY');
        if($key === null) {
            throw new \Exception('reCAPTCHA site key not set');
        }
        return $key;
    }

}