<?php

namespace App\Requests\Discourse;

use App\Library\API\APIRequest;

abstract class DiscourseAPIRequest extends APIRequest
{
    public function getPath(): string
    {
        return 'https://forums.projectcitybuild.com/';
    }
}