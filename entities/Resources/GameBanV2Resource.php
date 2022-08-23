<?php

namespace Entities\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GameBanV2Resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->game_ban_id,
            'server_id' => $this->server_id,
            'banned_player_id' => $this->banned_player_id,
            'banned_player_alias' => $this->banned_alias_at_time,
            'banner_player_id' => $this->staff_player_id,
            'reason' => $this->reason,
            'is_active' => (bool) $this->is_active,
            'expires_at' => isset($this->expires_at) ? $this->expires_at->timestamp : null,
            'created_at' => $this->created_at->timestamp,
            'updated_at' => $this->updated_at->timestamp,

            'unban' => new GameUnbanV1Resource($this->whenLoaded('unban')),
        ];
    }
}
