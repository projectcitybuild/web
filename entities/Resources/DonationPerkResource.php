<?php

namespace Entities\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class DonationPerkResource extends JsonResource
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
            'donation_perks_id' => $this->donation_perks_id,
            'is_active' => $this->is_active,
            'expires_at' => $this->expires_at->gettimestamp(),
            'created_at' => $this->created_at->getTimestamp(),
            'updated_at' => $this->updated_at->getTimestamp(),

            'donation_tier' => new DonationTierResource($this->whenLoaded('donationTier')),
        ];
    }
}
