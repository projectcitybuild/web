<?php

namespace App\Http\Controllers\Api\v1\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class MinecraftLootBoxResource extends JsonResource
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
            'minecraft_loot_box_id' => $this->minecraft_loot_box_id,
            'donation_tier_id' => $this->donation_tier_id,
            'loot_box_name' => $this->loot_box_name,
            'quantity' => $this->quantity,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->getTimestamp(),
            'updated_at' => $this->updated_at->getTimestamp(),
        ];
    }
}
