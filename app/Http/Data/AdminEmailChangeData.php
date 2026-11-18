<?php

namespace App\Http\Data;

use App\Models\EmailChange;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class AdminEmailChangeData extends Data
{
    public function __construct(
        public int $id,
        public int $account_id,
        public string $token,
        public string $email,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $created_at,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $updated_at,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public CarbonInterface $expires_at,
    ) {}

    public static function fromModel(EmailChange $emailChange): self
    {
        return new self(
            id: $emailChange->id,
            account_id: $emailChange->account_id,
            token: $emailChange->token,
            email: $emailChange->email,
            created_at: $emailChange->created_at,
            updated_at: $emailChange->updated_at,
            expires_at: $emailChange->expires_at,
        );
    }
}
