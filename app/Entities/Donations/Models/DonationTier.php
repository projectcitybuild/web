<?php

namespace App\Entities\Donations\Models;

use App\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function minecraftLootBoxes(): HasMany
    {
        return $this->hasMany(MinecraftLootBox::class, 'donation_tier_id', 'donation_tier_id');
    }
}
