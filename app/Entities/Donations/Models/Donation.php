<?php

namespace App\Entities\Donations\Models;

use App\Entities\Accounts\Models\Account;
use App\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class Donation extends Model
{
    /**
     * Amount that needs to be donated to be granted
     * lifetime perks
     */
    const LIFETIME_REQUIRED_AMOUNT = 30;

    /**
     * Amount that needs to be donated to receive one month
     * worth of donator perks
     */
    const ONE_MONTH_REQUIRED_AMOUNT = 3;


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

    public function account() : HasOne
    {
        return $this->hasOne(Account::class, 'account_id', 'account_id');
    }

    public function perks() : BelongsToMany
    {
        return $this->belongsToMany(DonationPerk::class, 'donator_perks', 'donation_id', 'donation_id');
    }
}