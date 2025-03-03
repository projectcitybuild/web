<?php

namespace App\Domains\Donations\Data\Payloads;

final class StripePaginatedResponse
{
    public function __construct(
        public String $object,
        public array $data,
        public bool $hasMore,
    ) {}

    public static function fromJson(array $payload): StripePaginatedResponse
    {
        return new StripePaginatedResponse(
            object: $payload['object'],
            data: $payload['data'],
            hasMore: $payload['has_more'] ?? false,
        );
    }
}
