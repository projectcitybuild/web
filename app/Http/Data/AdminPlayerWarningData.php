<?php

namespace App\Http\Data;

use App\Models\PlayerWarning;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class AdminPlayerWarningData extends Data
{
    public function __construct(
        public int $id,
        public int $warned_player_id,
        public int $warner_player_id,
        public string $reason,
        public ?string $additional_info,
        public int $weight,
        public bool $is_acknowledged,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $acknowledged_at,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $created_at,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $updated_at,
        public MinecraftPlayerData $warned_player,
        public MinecraftPlayerData $warner_player,
    ) {}

    public static function fromModel(PlayerWarning $warning): self
    {
        return new self(
            id: $warning->id,
            warned_player_id: $warning->warned_player_id,
            warner_player_id: $warning->warner_player_id,
            reason: $warning->reason,
            additional_info: $warning->additional_info,
            weight: $warning->weight,
            is_acknowledged: $warning->is_acknowledged,
            acknowledged_at: $warning->acknowledged_at,
            created_at: $warning->created_at,
            updated_at: $warning->updated_at,
            warned_player: MinecraftPlayerData::fromModel($warning->warnedPlayer),
            warner_player: MinecraftPlayerData::fromModel($warning->warnerPlayer),
        );
    }
}
