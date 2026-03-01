<?php

namespace App\Models;

use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class StripeProduct extends Model
{
    use HasFactory;
    use HasStaticTable;

    protected $table = 'stripe_products';
    protected $fillable = [
        'price_id',
        'product_id',
        'donation_tier_id',
        'created_at',
        'updated_at',
    ];

    public function donationTier(): BelongsTo
    {
        return $this->belongsTo(
            related: DonationTier::class,
            foreignKey: 'donation_tier_id',
        );
    }
}
