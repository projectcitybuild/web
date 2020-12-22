<?php

namespace App\Entities\Servers\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     *
     * @return array
     */
    public function toArray($request)
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
            // 'is_querying'           => (bool)$this->is_querying,
            'is_visible' => (bool) $this->is_visible,
            'display_order' => $this->display_order,
            // 'created_at'            => $this->created_at->getTimestamp(),
            // 'updated_at'            => $this->updated_at->getTimestamp(),

            'status' => $this->whenLoaded('latestStatus'),
        ];
    }
}
