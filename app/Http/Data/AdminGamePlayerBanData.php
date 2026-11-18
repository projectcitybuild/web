<?php

namespace App\Http\Data;

use App\Domains\Bans\Data\UnbanType;
use App\Models\GamePlayerBan;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class AdminGamePlayerBanData extends Data
{
    public function __construct(
        public int $id,
        public int $banned_player_id,
        public ?string $banned_alias_at_time,
        public ?int $banner_player_id,
        public string $reason,
        public ?string $additional_info,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $expires_at,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $created_at,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $updated_at,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $unbanned_at,
        public ?int $unbanner_player_id,
        public ?UnbanType $unban_type,
        public MinecraftPlayerData $banned_player,
        public Optional|MinecraftPlayerData|null $banner_player,
        public Optional|MinecraftPlayerData|null $unbanner_player,
    ) {}

    public static function fromModel(GamePlayerBan $ban): self
    {
        return new self(
            id: $ban->id,
            banned_player_id: $ban->banned_player_id,
            banned_alias_at_time: $ban->banned_alias_at_time,
            banner_player_id: $ban->banner_player_id,
            reason: $ban->reason,
            additional_info: $ban->additional_info,
            expires_at: $ban->expires_at,
            created_at: $ban->created_at,
            updated_at: $ban->updated_at,
            unbanned_at: $ban->unbanned_at,
            unbanner_player_id: $ban->unbanner_player_id,
            unban_type: $ban->unban_type,
            banned_player: MinecraftPlayerData::fromModel($ban->bannedPlayer),
            banner_player: self::loadedPlayer($ban, 'bannerPlayer'),
            unbanner_player: self::loadedPlayer($ban, 'unbannerPlayer'),
        );
    }

    private static function loadedPlayer(GamePlayerBan $ban, string $relation): Optional|MinecraftPlayerData|null
    {
        if (! $ban->relationLoaded($relation)) {
            return Optional::create();
        }

        $player = $ban->getRelation($relation);

        return $player === null ? null : MinecraftPlayerData::fromModel($player);
    }
}
