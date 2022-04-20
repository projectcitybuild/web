<?php

namespace Entities\Models\Eloquent;

use App\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property string product_id
 * @property int donation_tier_id
 * @property ?DonationTier donationTier
 */
final class StripeProduct extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stripe_products';

    protected $primaryKey = 'price_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'price_id',
        'product_id',
        'donation_tier_id',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
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
