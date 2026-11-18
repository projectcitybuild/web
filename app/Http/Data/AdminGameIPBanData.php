<?php

namespace App\Http\Data;

use App\Domains\Bans\Data\UnbanType;
use App\Models\GameIPBan;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class AdminGameIPBanData extends Data
{
    public function __construct(
        public int $id,
        public ?int $banner_player_id,
        public ?int $unbanner_player_id,
        public string $ip_address,
        public ?string $reason,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $created_at,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $updated_at,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $unbanned_at,
        public ?UnbanType $unban_type,
        public Optional|MinecraftPlayerData|null $banner_player,
        public Optional|MinecraftPlayerData|null $unbanner_player,
    ) {}

    public static function fromModel(GameIPBan $ban): self
    {
        return new self(
            id: $ban->id,
            banner_player_id: $ban->banner_player_id,
            unbanner_player_id: $ban->unbanner_player_id,
            ip_address: $ban->ip_address,
            reason: $ban->reason,
            created_at: $ban->created_at,
            updated_at: $ban->updated_at,
            unbanned_at: $ban->unbanned_at,
            unban_type: $ban->unban_type instanceof UnbanType
                ? $ban->unban_type
                : UnbanType::tryFrom($ban->unban_type),
            banner_player: self::loadedPlayer($ban, 'bannerPlayer'),
            unbanner_player: self::loadedPlayer($ban, 'unbannerPlayer'),
        );
    }

    private static function loadedPlayer(GameIPBan $ban, string $relation): Optional|MinecraftPlayerData|null
    {
        if (! $ban->relationLoaded($relation)) {
            return Optional::create();
        }

        $player = $ban->getRelation($relation);

        return $player === null ? null : MinecraftPlayerData::fromModel($player);
    }
}
