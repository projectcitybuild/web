<?php

namespace App\Models;

use App\Domains\PlayerMail\Data\MailCategory;
use Database\Factories\PlayerMailFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PlayerMail extends Model
{
    /** @use HasFactory<PlayerMailFactory> */
    use HasFactory;

    protected $fillable = [
        'sender_player_id',
        'sender_name',
        'created_by_account_id',
        'category',
        'subject',
        'body',
    ];
    protected $casts = [
        'category' => MailCategory::class,
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(MinecraftPlayer::class, 'sender_player_id');
    }

    public function createdByAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'created_by_account_id');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(PlayerMailRecipient::class, 'mail_id');
    }

    public function broadcast(): HasOne
    {
        return $this->hasOne(PlayerMailBroadcast::class, 'mail_id');
    }
}
