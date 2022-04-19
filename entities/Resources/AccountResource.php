<?php

namespace Entities\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class AccountResource extends JsonResource
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
            'account_id' => $this->account_id,
            'email' => $this->email,
            'username' => $this->username,
            'last_login_at' => $this->last_login_at,
            'created_at' => $this->created_at->getTimestamp(),
            'updated_at' => $this->updated_at->getTimestamp(),

            'groups' => GroupResource::collection($this->whenLoaded('groups')),
        ];
    }
}
