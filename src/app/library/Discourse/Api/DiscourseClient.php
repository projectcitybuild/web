<?php
namespace App\Library\Discourse\Api;

use GuzzleHttp\Client;

class DiscourseClient extends Client {

    public function __construct() {
        parent::__construct([
            'base_uri' => 'https://forums.projectcitybuild.com/',
        ]);
    }

}