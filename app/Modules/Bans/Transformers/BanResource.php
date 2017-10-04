<?php

namespace App\Modules\Bans\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class BanResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return [
            'server_id'         => $this->server_id,
            'player_alias_id'   => $this->player_game_user_id,
            'banner_alias_id'   => $this->staff_game_user_id,
            'reason'            => $this->reason,
            'is_active'         => (bool)$this->is_active,
            'is_global_ban'     => (bool)$this->is_global_ban,
            'expires_at'        => isset($this->expires_at) ? $this->expires_at->getTimestamp() : null,
            'created_at'        => $this->created_at->getTimestamp(),
            'updated_at'        => $this->updated_at->getTimestamp(),
        ];
    }
}