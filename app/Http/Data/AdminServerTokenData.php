<?php

namespace App\Http\Data;

use App\Models\ServerToken;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class AdminServerTokenData extends Data
{
    public function __construct(
        public int $id,
        public string $token,
        public ?string $description,
        public ?string $allowed_ips,
        public int $server_id,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $created_at,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $updated_at,
    ) {}

    public static function fromModel(ServerToken $serverToken): self
    {
        return new self(
            id: $serverToken->id,
            token: $serverToken->token,
            description: $serverToken->description,
            allowed_ips: $serverToken->allowed_ips,
            server_id: $serverToken->server_id,
            created_at: $serverToken->created_at,
            updated_at: $serverToken->updated_at,
        );
    }
}
