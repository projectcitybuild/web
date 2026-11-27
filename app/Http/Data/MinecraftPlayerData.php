<?php

namespace App\Http\Data;

use App\Domains\Players\Data\MoveMode;
use App\Domains\Players\Data\TeleportMode;
use App\Models\MinecraftPlayer;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class MinecraftPlayerData extends Data
{
    public function __construct(
        public int $id,
        public string $uuid,
        public ?string $alias,
        public ?string $nickname,
        public int $muted,
        public float $walk_speed,
        public float $fly_speed,
        public ?TeleportMode $teleport_mode,
        public ?MoveMode $move_mode,
        public ?string $last_location_world_uuid,
        public ?float $last_location_x,
        public ?float $last_location_y,
        public ?float $last_location_z,
        public ?float $last_location_pitch,
        public ?float $last_location_yaw,
        public int $sessions,
        public Optional|int $votes_count,
        public int $play_time,
        public int $afk_time,
        public int $blocks_placed,
        public int $blocks_destroyed,
        public int $blocks_travelled,
        public int $deaths,
        public int $mob_kills,
        public int $player_kills,
        public ?int $account_id,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $last_seen_at,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $last_connected_at,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $created_at,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $updated_at,
        public Optional|AdminAccountData|null $account,
    ) {}

    public static function fromModel(MinecraftPlayer $player): self
    {
        $votesCount = $player->getAttribute('votes_count');

        return new self(
            id: $player->id,
            uuid: $player->uuid,
            alias: $player->alias,
            nickname: $player->nickname,
            muted: (int) $player->muted,
            walk_speed: (float) $player->walk_speed,
            fly_speed: (float) $player->fly_speed,
            teleport_mode: $player->teleport_mode,
            move_mode: $player->move_mode,
            last_location_world_uuid: $player->last_location_world_uuid,
            last_location_x: $player->last_location_x,
            last_location_y: $player->last_location_y,
            last_location_z: $player->last_location_z,
            last_location_pitch: $player->last_location_pitch,
            last_location_yaw: $player->last_location_yaw,
            sessions: $player->sessions,
            votes_count: $votesCount === null ? Optional::create() : (int) $votesCount,
            play_time: $player->play_time,
            afk_time: $player->afk_time,
            blocks_placed: $player->blocks_placed,
            blocks_destroyed: $player->blocks_destroyed,
            blocks_travelled: $player->blocks_travelled,
            deaths: $player->deaths,
            mob_kills: $player->mob_kills,
            player_kills: $player->player_kills,
            account_id: $player->account_id,
            last_seen_at: $player->last_seen_at,
            last_connected_at: $player->last_connected_at,
            created_at: $player->created_at,
            updated_at: $player->updated_at,
            account: match (true) {
                ! $player->relationLoaded('account') => Optional::create(),
                $player->account === null => null,
                default => AdminAccountData::fromModel($player->account, canViewEmail: true),
            },
        );
    }
}
