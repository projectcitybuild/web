<?php

namespace Entities\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlayerWarningResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'warned_player_id' => $this->warned_player_id,
            'warner_player_id' => $this->warner_player_id,
            'reason' => $this->reason,
            'weight' => $this->weight,
            'is_acknowledged' => $this->is_acknowledged,
            'created_at' => $this->created_at->timestamp,
            'updated_at' => $this->updated_at->timestamp,
        ];
    }
}
