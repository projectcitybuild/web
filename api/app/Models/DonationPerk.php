<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use function now;

final class DonationPerk extends Model
{
    use HasFactory;

    protected $table = 'donation_perk';

    protected $fillable = [
        'donation_id',
        'account_id',
        'donation_tier_id',
        'expires_at',
        'created_at',
        'updated_at',
    ];

    protected $dates = [
        'expires_at',
        'created_at',
        'updated_at',
    ];

    public function isActive(): bool
    {
        if ($this->expires_at !== null && now()->gte($this->expires_at)) {
            return false;
        }
        return $this->is_active;
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(related: Account::class);
    }

    public function donation(): BelongsTo
    {
        return $this->belongsTo(related: Donation::class);
    }

    public function donationTier(): BelongsTo
    {
        return $this->belongsTo(related: DonationTier::class);
    }
}
