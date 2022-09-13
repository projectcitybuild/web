<?php

namespace Entities\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class MinecraftPlayerAliasResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->players_minecraft_alias_id,
            'alias' => $this->alias,
            'registered_at' => $this->registered_at->getTimestamp(),
            'created_at' => $this->created_at->getTimestamp(),
            'updated_at' => $this->updated_at->getTimestamp(),

            'player' => MinecraftPlayerResource::make(
                $this->whenLoaded('minecraftPlayer'),
            ),
        ];
    }
}
