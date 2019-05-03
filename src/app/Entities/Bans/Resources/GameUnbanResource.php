<?php

namespace App\Entities\Bans\Resources;

use Illuminate\Http\Resources\Json\Resource;

class GameUnbanResource extends Resource
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
            'game_unban_id'     => $this->game_unban_id,
            'game_ban_id'       => $this->game_ban_id,
            'staff_player_id'   => $this->staff_player_id,
            'staff_player_type' => $this->staff_player_type,
            'created_at'        => $this->created_at->timestamp,
            'updated_at'        => $this->updated_at->timestamp,
        ];
    }
}
