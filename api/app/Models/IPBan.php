<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class IPBan extends Model
{
    use HasFactory;

    protected $table = 'ip_ban';

    protected $fillable = [
        'banner_player_id',
        'ip_address',
        'reason',
        'created_at',
        'updated_at',
        'unbanned_at',
        'unbanner_player_id',
        'unban_type',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'unbanned_at',
    ];

    public function bannerPlayer(): BelongsTo
    {
        return $this->belongsTo(
            related: Player::class,
            foreignKey: 'banner_player_id',
            ownerKey: Player::primaryKey(),
        );
    }

    public function unbannerPlayer(): BelongsTo
    {
        return $this->belongsTo(
            related: Player::class,
            foreignKey: 'unbanner_player_id',
            ownerKey: Player::primaryKey(),
        );
    }
}
