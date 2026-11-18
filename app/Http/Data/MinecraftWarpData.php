<?php

namespace App\Http\Data;

use App\Models\MinecraftWarp;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class MinecraftWarpData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $world,
        public float $x,
        public float $y,
        public float $z,
        public string $pitch,
        public string $yaw,

        #[WithTransformer(
            DateTimeInterfaceTransformer::class,
            format: 'Y-m-d\\TH:i:s.u\\Z',
            setTimeZone: 'UTC'
        )]
        public ?CarbonInterface $created_at,

        #[WithTransformer(
            DateTimeInterfaceTransformer::class,
            format: 'Y-m-d\\TH:i:s.u\\Z',
            setTimeZone: 'UTC'
        )]
        public ?CarbonInterface $updated_at,
    ) {}

    public static function fromModel(MinecraftWarp $warp): self
    {
        return new self(
            id: $warp->id,
            name: $warp->name,
            world: $warp->world,
            x: $warp->x,
            y: $warp->y,
            z: $warp->z,
            pitch: $warp->pitch,
            yaw: $warp->yaw,
            created_at: $warp->created_at,
            updated_at: $warp->updated_at,
        );
    }
}
