<?php

namespace App\Models;

use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class StripeProduct extends Model
{
    use HasStaticTable;

    protected $table = 'stripe_products';

    protected $primaryKey = 'price_id';

    protected $fillable = [
        'price_id',
        'product_id',
        'donation_tier_id',
    ];

    public $timestamps = false;

    public function donationTier(): HasOne
    {
        return $this->hasOne(
            related: DonationTier::class,
            foreignKey: 'donation_tier_id',
            localKey: 'donation_tier_id',
        );
    }
}
