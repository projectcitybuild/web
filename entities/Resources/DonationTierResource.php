<?php

namespace Entities\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class DonationTierResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'donation_tier_id' => $this->donation_tier_id,
            'name' => $this->name,
        ];
    }
}
