<?php

namespace App\Http\Data;

use App\Models\Donation;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class AdminDonationData extends Data
{
    public function __construct(
        public int $id,
        public ?int $account_id,
        public float $amount,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $created_at,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $updated_at,
        public Optional|AdminAccountData|null $account,
        public Optional|AdminPaymentData|null $payment,
    ) {}

    public static function fromModel(Donation $donation): self
    {
        return new self(
            id: $donation->id,
            account_id: $donation->account_id,
            amount: (float) $donation->amount,
            created_at: $donation->created_at,
            updated_at: $donation->updated_at,
            account: match (true) {
                ! $donation->relationLoaded('account') => Optional::create(),
                $donation->account === null => null,
                default => AdminAccountData::fromModel($donation->account, canViewEmail: true),
            },
            payment: match (true) {
                ! $donation->relationLoaded('payment') => Optional::create(),
                $donation->payment === null => null,
                default => AdminPaymentData::fromModel($donation->payment),
            },
        );
    }
}
