<?php

namespace App\Requests\Discourse;

use App\Library\API\HttpMethodType;

final class DiscourseExternalUserIdRequest extends DiscourseAPIRequest
{
    private $pcbId;

    public function __construct(int $pcbId)
    {
        $this->pcbId = $pcbId;
    }

    public function getPath() : string
    {
        return parent::getPath() . 'users/by-external/' . $this->pcbId . '.json';
    }

    public function getHttpMethod() : HttpMethodType
    {
        return new HttpMethodType(HttpMethodType::GET);
    }
}