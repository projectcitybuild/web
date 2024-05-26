<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Donation extends Model
{
    use HasFactory;

    protected $table = 'donation';

    protected $fillable = [
        'account_id',
        'amount',
        'created_at',
        'updated_at',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(related: Account::class);
    }

    public function perks(): HasMany
    {
        return $this->hasMany(related: DonationPerk::class);
    }
}
