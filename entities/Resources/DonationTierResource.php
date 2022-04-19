<?php

namespace Entities\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class DonationTierResource extends JsonResource
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
            'donation_tier_id' => $this->donation_tier_id,
            'name' => $this->name,
        ];
    }
}
