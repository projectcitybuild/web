<?php

namespace App\Http\Data;

use App\Models\Account;
use App\Models\PlayerReport;
use Spatie\LaravelData\Data;

final class AdminPlayerReportData extends Data
{
    /**
     * @param  list<array{id: int, name: string, alias: ?string, uuid: ?string}>  $assignees
     * @param  list<array{id: int, name: string, url: string, mime_type: string, preview_url: ?string}>  $attachments
     */
    public function __construct(
        public int $id,
        public string $reporter_name,
        public string $reporter_contact,
        public ?string $reporter_email,
        public ?string $reporter_account,
        public string $reported_player_name,
        public string $subject,
        public string $details,
        public string $visibility,
        public string $status,
        public ?string $completion_message,
        public ?string $resolved_at,
        public array $assignees,
        public array $attachments,
        public string $created_at,
    ) {}

    public static function fromModel(PlayerReport $report): self
    {
        return new self(
            id: $report->id,
            reporter_name: $report->reporter_name,
            reporter_contact: $report->reporter_contact,
            reporter_email: $report->reporter_email,
            reporter_account: $report->reporterAccount?->username,
            reported_player_name: $report->reported_player_name,
            subject: $report->subject,
            details: $report->details,
            visibility: $report->visibility->value,
            status: $report->status->value,
            completion_message: $report->completion_message,
            resolved_at: $report->resolved_at?->toIso8601String(),
            assignees: $report->assignees
                ->map(function (Account $account): array {
                    $player = $account->minecraftAccount->first();

                    return [
                        'id' => $account->id,
                        'name' => $account->username,
                        'alias' => $player?->alias,
                        'uuid' => $player?->uuid,
                    ];
                })
                ->values()
                ->all(),
            attachments: $report->attachments
                ->map(function ($attachment) use ($report): array {
                    $isPreviewableImage = in_array(
                        $attachment->mime_type,
                        ['image/jpeg', 'image/png', 'image/webp'],
                        true,
                    );
                    return [
                        'id' => $attachment->id,
                        'name' => $attachment->original_name,
                        'url' => route('review.player-reports.attachments.download', [$report, $attachment]),
                        'mime_type' => $attachment->mime_type,
                        'preview_url' => $isPreviewableImage
                            ? route('review.player-reports.attachments.preview', [$report, $attachment])
                            : null,
                    ];
                })
                ->values()
                ->all(),
            created_at: $report->created_at->toIso8601String(),
        );
    }
}
