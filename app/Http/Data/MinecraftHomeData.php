<?php

namespace App\Http\Data;

use App\Models\MinecraftHome;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class MinecraftHomeData extends Data
{
    public function __construct(
        public int $id,
        public int $player_id,
        public string $name,
        public string $world,
        public float $x,
        public float $y,
        public float $z,
        public string $pitch,
        public string $yaw,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $created_at,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $updated_at,
        public Optional|MinecraftPlayerData|null $player,
    ) {}

    public static function fromModel(MinecraftHome $home): self
    {
        $player = match (true) {
            ! $home->relationLoaded('player') => Optional::create(),
            $home->player === null => null,
            default => MinecraftPlayerData::fromModel($home->player),
        };

        return new self(
            id: $home->id,
            player_id: $home->player_id,
            name: $home->name,
            world: $home->world,
            x: $home->x,
            y: $home->y,
            z: $home->z,
            pitch: $home->pitch,
            yaw: $home->yaw,
            created_at: $home->created_at,
            updated_at: $home->updated_at,
            player: $player,
        );
    }
}
