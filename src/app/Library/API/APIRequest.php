<?php

namespace App\Library\API;

abstract class APIRequest
{
    abstract function getPath() : string;
    abstract function getHttpMethod() : HttpMethodType;

    public function getRequestBody() : ?array
    {
        return null;
    }

    public function getRequestURLParams() : array
    {
        return [];
    }
}