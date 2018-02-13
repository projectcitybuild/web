<?php

namespace App\Modules\Bans\Resources;

use Illuminate\Http\Resources\Json\Resource;

class GameBanResource extends Resource {
    
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return [
            'game_ban_id'           => $this->game_ban_id,
            'server_id'             => $this->server_id,
            'banned_player_id'      => $this->banned_player_id,
            'banned_player_type'    => $this->banned_player_type,
            'banned_alias_at_time'  => $this->banned_alias_at_time,
            'staff_player_id'       => $this->staff_player_id,
            'staff_player_type'     => $this->staff_player_type,
            'reason'                => $this->reason,
            'is_active'             => (bool)$this->is_active,
            'is_global_ban'         => (bool)$this->is_global_ban,
            'expires_at'            => isset($this->expires_at) ? $this->expires_at->timestamp : null,
            'created_at'            => $this->created_at->getTimestamp(),
            'updated_at'            => $this->updated_at->getTimestamp(),

            'unban' => new GameUnbanResource($this->whenLoaded('unban')),
        ];
    }
}