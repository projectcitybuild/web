<?php

namespace App\Entities\Donations\Models;

use App\Entities\Accounts\Models\Account;
use App\Model;

final class DonationPaymentSession extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'donation_payment_sessions';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'donation_payment_session_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id',
        'donation_tier_id',
        'donation_perks_id',
        'session_id',
        'stripe_transaction_id',
        'stripe_price_id',
        'number_of_months',
        'is_processed',
        'is_refunded',
        'is_subscription',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function account()
    {
        return $this->hasOne(Account::class, 'account_id', 'account_id');
    }

    public function donationTier()
    {
        return $this->hasOne(DonationTier::class, 'donation_tier_id', 'donation_tier_id');
    }

    public function donationPerk()
    {
        return $this->hasOne(DonationPerk::class, 'donation_perks_id', 'donation_perks_id');
    }
}
