<?php

namespace Tests\Support;

use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/** @mixin TestCase */
trait ApiBanRequests
{
    protected function postBan(array $data): TestResponse
    {
        return $this->withServerToken()->post('http://api.localhost/v3/bans/uuid', $data);
    }

    protected function putBan(int $id, array $data): TestResponse
    {
        return $this->withServerToken()->put("http://api.localhost/v3/bans/uuid/{$id}", $data);
    }
}
