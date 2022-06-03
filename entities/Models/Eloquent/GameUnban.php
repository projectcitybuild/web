<?php

namespace Entities\Models\Eloquent;

use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

final class GameUnban extends Model
{
    use HasFactory;

    protected $table = 'game_network_unbans';

    protected $primaryKey = 'game_unban_id';

    protected $fillable = [
        'game_ban_id',
        'staff_player_id',
        'staff_player_type',
    ];

    protected $hidden = [

    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function ban()
    {
        return $this->belongsTo(GameBan::class, 'game_ban_id', 'game_ban_id');
    }

    public function staffPlayer()
    {
        return $this->morphTo(null, 'staff_player_type', 'staff_player_id');
    }
}
