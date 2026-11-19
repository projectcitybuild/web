<?php

namespace App\Http\Data;

use App\Models\DonationPerk;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class AdminDonationPerkData extends Data
{
    public function __construct(
        public int $id,
        public int $account_id,
        public int $donation_id,
        public ?int $donation_tier_id,
        public bool $is_active,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $expires_at,
        public ?string $account_username,
        public ?string $donation_tier_name,
    ) {}

    public static function fromModel(DonationPerk $perk): self
    {
        return new self(
            id: $perk->id,
            account_id: $perk->account_id,
            donation_id: $perk->donation_id,
            donation_tier_id: $perk->donation_tier_id,
            is_active: $perk->is_active,
            expires_at: $perk->expires_at,
            account_username: $perk->account?->username,
            donation_tier_name: $perk->donationTier?->name,
        );
    }
}
