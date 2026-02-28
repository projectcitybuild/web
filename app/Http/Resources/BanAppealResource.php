<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BanAppealResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status?->value,
            'email' => $this->email,
            'minecraft_uuid' => $this->minecraft_uuid,
            'date_of_ban' => $this->date_of_ban,
            'ban_reason' => $this->ban_reason,
            'unban_reason' => $this->unban_reason,
            'decided_at' => $this->decided_at?->toDateTimeString(),
            'decision_note' => $this->decision_note,
            'account' => $this->whenLoaded('account', fn () => new AccountResource($this->account)),
            'game_player_ban' => $this->whenLoaded('gamePlayerBan'),
            'decider_player' => $this->whenLoaded('deciderPlayer'),
        ];
    }
}
