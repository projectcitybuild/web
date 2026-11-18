<?php

namespace App\Http\Data;

use App\Models\Payment;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class AdminPaymentData extends Data
{
    public function __construct(
        public int $payment_id,
        public string $paid_currency,
        public int $paid_unit_amount,
        public string $original_currency,
        public int $original_unit_amount,
        public int $unit_quantity,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $created_at,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $updated_at,
    ) {}

    public static function fromModel(Payment $payment): self
    {
        return new self(
            payment_id: $payment->id,
            paid_currency: $payment->paid_currency,
            paid_unit_amount: $payment->paid_unit_amount,
            original_currency: $payment->original_currency,
            original_unit_amount: $payment->original_unit_amount,
            unit_quantity: $payment->unit_quantity,
            created_at: $payment->created_at,
            updated_at: $payment->updated_at,
        );
    }
}
