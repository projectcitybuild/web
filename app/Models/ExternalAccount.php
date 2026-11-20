<?php

namespace App\Models;

use Database\Factories\ExternalAccountFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ExternalAccount extends Model
{
    /** @use HasFactory<ExternalAccountFactory> */
    use HasFactory;

    public const PROVIDER_DISCORD = 'discord';

    protected $fillable = [
        'account_id',
        'provider',
        'provider_user_id',
        'username',
        'verified_at',
    ];
    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
