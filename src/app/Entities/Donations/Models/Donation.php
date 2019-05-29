<?php

namespace App\Entities\Donations\Models;

use App\Model;

/**
 * App\Entities\Donations\Models\Donation
 *
 * @property int $donation_id
 * @property int|null $account_id
 * @property float $amount Amount donated in dollars
 * @property \Illuminate\Support\Carbon|null $perks_end_at Expiry date of donator perks if not a lifetime threshold donation
 * @property mixed $is_lifetime_perks Whether the user gains donator perks for life
 * @property mixed $is_active Whether the donation perks are currently active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $perk_recipient_account_id
 * @property mixed $is_anonymous
 * @property mixed $is_countable Whether this donation should be used in calculating statistics/totals
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Donations\Models\Donation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Donations\Models\Donation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Donations\Models\Donation query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Donations\Models\Donation whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Donations\Models\Donation whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Donations\Models\Donation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Donations\Models\Donation whereDonationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Donations\Models\Donation whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Donations\Models\Donation whereIsAnonymous($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Donations\Models\Donation whereIsCountable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Donations\Models\Donation whereIsLifetimePerks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Donations\Models\Donation wherePerkRecipientAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Donations\Models\Donation wherePerksEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Donations\Models\Donation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Donation extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'donations';

    protected $primaryKey = 'donation_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id',
        'amount',
        'perks_end_at',
        'is_lifetime_perks',
        'is_active',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = [
        'perks_end_at',
        'created_at',
        'updated_at',
    ];
}
