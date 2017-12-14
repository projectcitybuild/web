<?php

namespace App\Modules\Users\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class UserAliasResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return [
            'user_alias_id'         => $this->user_alias_id,
            'user_alias_type_id'    => $this->user_alias_type_id,
            'game_user_id'          => $this->game_user_id,
            'alias'                 => $this->alias,
            'created_at'            => $this->created_at->getTimestamp(),
            'updated_at'            => $this->updated_at->getTimestamp(),
        ];
    }
}