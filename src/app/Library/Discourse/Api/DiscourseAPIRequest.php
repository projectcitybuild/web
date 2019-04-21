<?php

namespace Domains\Library\Discourse\Api;

abstract class DiscourseAPIRequest 
{
    /**
     * @var DiscourseClient
     */
    protected $client;

    public function __construct(DiscourseClient $client)
    {
        $this->client = $client;
    }
}