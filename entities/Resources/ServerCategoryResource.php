<?php

namespace Entities\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServerCategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'server_category_id' => $this->server_category_id,
            'name' => $this->name,
            'display_order' => $this->display_order,

            'servers' => ServerResource::collection($this->whenLoaded('servers')),
        ];
    }
}
