<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MinecraftPlayerResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'alias' => $this->alias,
            'nickname' => $this->nickname,
            'muted' => $this->muted,
            'walk_speed' => $this->walk_speed,
            'fly_speed' => $this->fly_speed,
            'teleport_mode' => $this->teleport_mode?->value,
            'move_mode' => $this->move_mode?->value,
            'last_location_world_uuid' => $this->last_location_world_uuid,
            'last_location_x' => $this->last_location_x,
            'last_location_y' => $this->last_location_y,
            'last_location_z' => $this->last_location_z,
            'last_location_pitch' => $this->last_location_pitch,
            'last_location_yaw' => $this->last_location_yaw,
            'sessions' => $this->sessions,
            'play_time' => $this->play_time,
            'afk_time' => $this->afk_time,
            'blocks_placed' => $this->blocks_placed,
            'blocks_destroyed' => $this->blocks_destroyed,
            'blocks_travelled' => $this->blocks_travelled,
            'account_id' => $this->account_id,
            'last_seen_at' => $this->last_seen_at,
            'last_connected_at' => $this->last_connected_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
