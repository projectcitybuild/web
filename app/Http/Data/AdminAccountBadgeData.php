<?php

namespace App\Http\Data;

use App\Models\Badge;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class AdminAccountBadgeData extends Data
{
    public function __construct(
        public int $id,
        public string $display_name,
        public string $unicode_icon,
        public bool $list_hidden,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $created_at,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $updated_at,
        public Optional|AdminAccountBadgePivotData|null $pivot,
    ) {}

    public static function fromModel(Badge $badge): self
    {
        return new self(
            id: $badge->id,
            display_name: $badge->display_name,
            unicode_icon: $badge->unicode_icon,
            list_hidden: $badge->list_hidden,
            created_at: $badge->created_at,
            updated_at: $badge->updated_at,
            pivot: $badge->pivot === null ? Optional::create() : AdminAccountBadgePivotData::fromModel($badge->pivot),
        );
    }
}
