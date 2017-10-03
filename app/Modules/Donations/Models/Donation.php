<?php

namespace App\Modules\Donations\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model {

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
        'forum_user_id',
        'amount',
        'perks_end_at',
        'prev_rank_id',
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
