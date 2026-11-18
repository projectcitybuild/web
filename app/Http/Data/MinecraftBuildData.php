<?php

namespace App\Http\Data;

use App\Models\MinecraftBuild;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class MinecraftBuildData extends Data
{
    public function __construct(
        public int $id,
        public int $player_id,
        public string $name,
        public ?string $description,
        public ?string $lore,
        public int $votes,
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

    public static function fromModel(MinecraftBuild $build): self
    {
        $player = match (true) {
            ! $build->relationLoaded('player') => Optional::create(),
            $build->player === null => null,
            default => MinecraftPlayerData::fromModel($build->player),
        };

        return new self(
            id: $build->id,
            player_id: $build->player_id,
            name: $build->name,
            description: $build->description,
            lore: $build->lore,
            votes: $build->votes,
            world: $build->world,
            x: $build->x,
            y: $build->y,
            z: $build->z,
            pitch: $build->pitch,
            yaw: $build->yaw,
            created_at: $build->created_at,
            updated_at: $build->updated_at,
            player: $player,
        );
    }
}
