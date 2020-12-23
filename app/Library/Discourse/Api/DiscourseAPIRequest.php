<?php

namespace App\Library\Discourse\Api;

abstract class DiscourseAPIRequest
{
    protected DiscourseClient $client;

    public function __construct(DiscourseClient $client)
    {
        $this->client = $client;
    }
}
