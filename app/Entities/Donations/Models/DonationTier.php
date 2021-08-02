<?php

namespace App\Entities\Donations\Models;

use App\Model;

final class DonationTier extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'donation_tiers';

    protected $primaryKey = 'donation_tier_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'min_donation_amount',
        'stripe_payment_price_id',
        'stripe_subscription_price_id',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
