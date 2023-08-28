<?php

namespace App\Models\Eloquent;

use Database\Factories\DonationFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Donation extends Model
{
    use HasFactory;

    protected $table = 'donations';

    protected $primaryKey = 'donation_id';

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

    protected static function newFactory(): Factory
    {
        return DonationFactory::new();
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(
            related: Account::class,
            foreignKey: 'account_id',
            ownerKey: 'account_id',
        );
    }

    public function perks(): HasMany
    {
        return $this->hasMany(
            related: DonationPerk::class,
            foreignKey: 'donation_id',
            localKey: 'donation_id',
        );
    }
}
