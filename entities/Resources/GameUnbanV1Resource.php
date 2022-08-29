<?php

namespace Entities\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GameUnbanV1Resource extends JsonResource
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
            'game_unban_id' => $this->game_unban_id,
            'game_ban_id' => $this->game_ban_id,
            'staff_player_id' => $this->staff_player_id,
            'created_at' => $this->created_at->timestamp,
            'updated_at' => $this->updated_at->timestamp,
        ];
    }
}
