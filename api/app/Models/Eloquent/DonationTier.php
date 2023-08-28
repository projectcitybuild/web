<?php

namespace App\Models\Eloquent;

use Database\Factories\DonationTierFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string name
 * @property int currency_reward
 */
final class DonationTier extends Model
{
    use HasFactory;

    protected $table = 'donation_tiers';

    protected $primaryKey = 'donation_tier_id';

    protected $fillable = [
        'name',
        'currency_reward',
    ];

    public $timestamps = false;

    protected static function newFactory(): Factory
    {
        return DonationTierFactory::new();
    }
}
