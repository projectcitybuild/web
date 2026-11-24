<?php

namespace App\Models;

use App\Domains\PlayerReports\Data\PlayerReportStatus;
use App\Domains\PlayerReports\Data\PlayerReportVisibility;
use Database\Factories\PlayerReportFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class PlayerReport extends Model
{
    /** @use HasFactory<PlayerReportFactory> */
    use HasFactory;

    protected $fillable = [
        'reporter_account_id',
        'reporter_name',
        'reporter_contact',
        'reporter_email',
        'reported_player_name',
        'subject',
        'details',
        'visibility',
        'status',
        'completion_message',
        'resolved_by_account_id',
        'resolved_at',
    ];
    protected $casts = [
        'status' => PlayerReportStatus::class,
        'visibility' => PlayerReportVisibility::class,
        'resolved_at' => 'datetime',
    ];

    public function reporterAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'reporter_account_id');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'resolved_by_account_id');
    }

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(Account::class, 'player_report_assignments')
            ->withPivot('assigned_by_account_id')
            ->withTimestamps();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(PlayerReportAttachment::class);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', PlayerReportStatus::OPEN->value);
    }
}
