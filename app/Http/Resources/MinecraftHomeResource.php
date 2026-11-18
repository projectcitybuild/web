<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MinecraftHomeResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'player_id' => $this->player_id,
            'name' => $this->name,
            'world' => $this->world,
            'x' => $this->x,
            'y' => $this->y,
            'z' => $this->z,
            'pitch' => $this->pitch,
            'yaw' => $this->yaw,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'player' => $this->whenLoaded('player', fn () => new MinecraftPlayerResource($this->player)),
        ];
    }
}
