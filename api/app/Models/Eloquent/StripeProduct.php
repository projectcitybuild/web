<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property string product_id
 * @property int donation_tier_id
 * @property ?DonationTier donationTier
 */
final class StripeProduct extends Model
{
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
