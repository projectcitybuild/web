<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class MinecraftPlayerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->player_minecraft_id,
            'uuid' => $this->uuid,
            'account_id' => $this->account_id,
            'last_synced_at' => $this->last_synced_at?->getTimestamp(),
            'last_seen_at' => $this->last_seen_at?->getTimestamp(),
            'created_at' => $this->created_at->getTimestamp(),
            'updated_at' => $this->updated_at->getTimestamp(),

            'aliases' => $this->whenLoaded(
                relationship: 'aliases',
                value: MinecraftPlayerAliasResource::collection($this->aliases),
            ),
        ];
    }
}
