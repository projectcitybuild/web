<?php

namespace App\Http\Data;

use App\Models\MinecraftConfig;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class MinecraftConfigApiData extends Data
{
    public function __construct(
        public ?int $id,
        public ?object $config,
        public ?int $version,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $created_at,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $updated_at,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $deleted_at,
    ) {}

    public static function fromModel(?MinecraftConfig $config): self
    {
        return new self(
            id: $config?->id,
            config: $config?->config === null ? null : (object) $config->config,
            version: $config?->version,
            created_at: $config?->created_at,
            updated_at: $config?->updated_at,
            deleted_at: $config?->deleted_at,
        );
    }
}
