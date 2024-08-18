<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GameIPBanResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'banner_player_id' => $this->banner_player_id,
            'ip_address' => $this->ip_address,
            'reason' => $this->reason,
            'created_at' => $this->created_at->timestamp,
            'updated_at' => $this->updated_at->timestamp,
            'unbanned_at' => $this->unbanned_at?->timestamp,
            'unbanner_player_id' => $this->unbanner_player_id,
            'unban_type' => $this->unban_type,
        ];
    }
}
