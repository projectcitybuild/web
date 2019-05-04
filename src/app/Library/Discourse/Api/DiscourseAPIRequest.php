<?php

namespace App\Library\Discourse\Api;

/**
 * @deprecated 1.11.0 Use APIClientProvider instead
 */
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