<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MinecraftWarpResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'world' => $this->world,
            'x' => $this->x,
            'y' => $this->y,
            'z' => $this->z,
            'pitch' => $this->pitch,
            'yaw' => $this->yaw,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
