<?php

namespace App\Http\Data;

use App\Models\AccountActivation;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class AdminAccountActivationData extends Data
{
    public function __construct(
        public int $id,
        public int $account_id,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $created_at,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $updated_at,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public CarbonInterface $expires_at,
    ) {}

    public static function fromModel(AccountActivation $activation): self
    {
        return new self(
            id: $activation->id,
            account_id: $activation->account_id,
            created_at: $activation->created_at,
            updated_at: $activation->updated_at,
            expires_at: $activation->expires_at,
        );
    }
}
