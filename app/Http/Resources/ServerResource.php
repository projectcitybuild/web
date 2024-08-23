<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'server_id' => $this->server_id,
            'server_category_id' => $this->server_category_id,
            'name' => $this->name,
            'ip' => $this->ip,
            'ip_alias' => $this->ip_alias,
            'port' => $this->port,
            'game_type' => $this->game_type,
            'is_port_visible' => (bool) $this->is_port_visible,
            'is_visible' => (bool) $this->is_visible,
            'display_order' => $this->display_order,

            'status' => $this->whenLoaded('latestStatus'),
        ];
    }
}
