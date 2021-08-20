<?php

namespace App\Entities\Donations\Models;

use App\Model;

final class MinecraftRedeemedLootBox extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'minecraft_redeemed_loot_boxes';

    protected $primaryKey = 'redeemed_loot_box_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id',
        'donation_perks_id',
        'created_at',
    ];

    protected $dates = [
        'created_at',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
