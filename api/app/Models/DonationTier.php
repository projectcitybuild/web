<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class DonationTier extends Model
{
    use HasFactory;

    protected $table = 'donation_tier';

    protected $fillable = [
        'name',
        'currency_reward',
    ];

    public $timestamps = false;
}
