<?php

namespace App\Entities\Servers\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServerCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     *
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'server_category_id' => $this->server_category_id,
            'name' => $this->name,
            'display_order' => $this->display_order,
            // 'created_at'            => $this->created_at->getTimestamp(),
            // 'updated_at'            => $this->updated_at->getTimestamp(),

            'servers' => ServerResource::collection($this->whenLoaded('servers')),
        ];
    }
}
