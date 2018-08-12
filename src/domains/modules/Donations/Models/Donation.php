<?php

namespace Domains\Modules\Donations\Models;

use Application\Model;

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
