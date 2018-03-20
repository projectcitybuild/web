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

    

}