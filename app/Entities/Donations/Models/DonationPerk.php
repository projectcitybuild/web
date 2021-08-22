<?php

namespace App\Entities\Donations\Models;

use App\Entities\Accounts\Models\Account;
use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class DonationPerk extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'donation_perks';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'donation_perks_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'donation_id',
        'account_id',
        'donation_tier_id',
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

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'expires_at',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_lifetime_perks' => 'boolean',
    ];

    public function isActive(): bool
    {
        if ($this->expires_at !== null && now()->lte($this->expires_at)) {
            return false;
        }

        return $this->is_active;
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class, 'donation_id', 'donation_id');
    }

    public function donationTier(): BelongsTo
    {
        return $this->belongsTo(DonationTier::class, 'donation_tier_id', 'donation_tier_id');
    }
}
