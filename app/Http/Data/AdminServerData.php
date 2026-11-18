<?php

namespace App\Http\Data;

use App\Models\Server;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class AdminServerData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $ip,
        public ?string $ip_alias,
        public ?string $port,
        public ?string $web_port,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $created_at,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $updated_at,
    ) {}

    public static function fromModel(Server $server): self
    {
        return new self(
            id: $server->id,
            name: $server->name,
            ip: $server->ip,
            ip_alias: $server->ip_alias,
            port: $server->port,
            web_port: $server->web_port,
            created_at: $server->created_at,
            updated_at: $server->updated_at,
        );
    }
}
