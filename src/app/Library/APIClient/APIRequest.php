<?php

namespace App\Library\APIClient;

abstract class APIRequest
{
    /**
     * Relative path to the endpoint (not including the base URL)
     *
     * @return String
     */
    public abstract function path(): String;

    /**
     * HTTP method
     *
     * @return APIRequestMethod
     */
    public abstract function method(): APIRequestMethod;

    /**
     * Generates an array of parameters to be serialized into the request
     *
     * @return array
     */
    public abstract function body(): ?array;
    
}