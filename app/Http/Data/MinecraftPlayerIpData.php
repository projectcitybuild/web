<?php

namespace App\Http\Data;

use App\Models\MinecraftPlayerIp;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class MinecraftPlayerIpData extends Data
{
    public function __construct(
        public int $id,
        public int $player_id,
        public string $ip,
        public int $times_connected,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $created_at,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $updated_at,
    ) {}

    public static function fromModel(MinecraftPlayerIp $ip): self
    {
        return new self(
            id: $ip->id,
            player_id: $ip->player_id,
            ip: $ip->ip,
            times_connected: $ip->times_connected,
            created_at: $ip->created_at,
            updated_at: $ip->updated_at,
        );
    }
}
