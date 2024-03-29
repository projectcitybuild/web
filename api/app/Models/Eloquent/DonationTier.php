<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
}
