<?php

namespace Entities\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class AccountResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'account_id' => $this->account_id,
            'username' => $this->username,
            'last_login_at' => $this->last_login_at?->getTimestamp(),
            'created_at' => $this->created_at->getTimestamp(),
            'updated_at' => $this->updated_at->getTimestamp(),

            'groups' => GroupResource::collection($this->whenLoaded('groups')),
        ];
    }
}
