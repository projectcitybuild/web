<?php

namespace Entities\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
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
            'group_id' => $this->group_id,
            'name' => $this->name,
            'alias' => $this->alias,
            'discourse_name' => $this->discourse_name,
            'minecraft_name' => $this->minecraft_name,
            'discord_name' => $this->discord_name,
            'is_default' => $this->is_default,
            'is_staff' => $this->is_staff,
            'is_admin' => $this->is_admin,
        ];
    }
}
