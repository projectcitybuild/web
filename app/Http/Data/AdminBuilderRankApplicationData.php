<?php

namespace App\Http\Data;

use App\Domains\BuilderRankApplications\Data\ApplicationStatus;
use App\Models\BuilderRankApplication;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class AdminBuilderRankApplicationData extends Data
{
    public function __construct(
        public int $id,
        public int $account_id,
        public string $minecraft_alias,
        public string $current_builder_rank,
        public string $build_location,
        public string $build_description,
        public ?string $additional_notes,
        public ApplicationStatus $status,
        public ?string $denied_reason,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $closed_at,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $created_at,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\\TH:i:s.u\\Z', setTimeZone: 'UTC')]
        public ?CarbonInterface $updated_at,
        public Optional|AdminAccountData|null $account,
    ) {}

    public static function fromModel(BuilderRankApplication $application): self
    {
        return new self(
            id: $application->id,
            account_id: $application->account_id,
            minecraft_alias: $application->minecraft_alias,
            current_builder_rank: $application->current_builder_rank,
            build_location: $application->build_location,
            build_description: $application->build_description,
            additional_notes: $application->additional_notes,
            status: $application->status,
            denied_reason: $application->denied_reason,
            closed_at: $application->closed_at,
            created_at: $application->created_at,
            updated_at: $application->updated_at,
            account: match (true) {
                ! $application->relationLoaded('account') => Optional::create(),
                $application->account === null => null,
                default => AdminAccountData::fromModel($application->account, canViewEmail: true),
            },
        );
    }
}
