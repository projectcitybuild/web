<?php

namespace App\Entities\Donations\Models;

use App\Entities\Accounts\Models\Account;
use App\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class DonationPerk extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'donation_perks';

    protected $primaryKey = 'donation_perk_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'donation_id',
        'account_id',
        'is_lifetime_perks',
        'is_active',
        'expires_at',
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
        'expires_at',
        'created_at',
        'updated_at',
    ];

    public function account() : HasOne {
        return $this->hasOne(Account::class, 'account_id', 'account_id');
    }
}
