<?php

namespace Entities\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GamePlayerBanResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'server_id' => $this->server_id,
            'banned_player_id' => $this->banned_player_id,
            'banned_player_alias' => $this->banned_alias_at_time,
            'banner_player_id' => $this->banner_player_id,
            'reason' => $this->reason,
            'expires_at' => $this->expires_at?->timestamp,
            'created_at' => $this->created_at->timestamp,
            'updated_at' => $this->updated_at->timestamp,
            'unbanned_at' => $this->unbanned_at?->timestamp,
            'unbanner_player_id' => $this->unbanner_player_id,
            'unban_type' => $this->unban_type,
        ];
    }
}
