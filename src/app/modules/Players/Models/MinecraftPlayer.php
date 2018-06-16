<?php

namespace App\Modules\Players\Models;

use App\Core\Model;
use App\Modules\Bans\BannableModelInterface;

class MinecraftPlayer extends Model implements BannableModelInterface {

    protected $table = 'players_minecraft';

    protected $primaryKey = 'player_minecraft_id';

    protected $fillable = [
        'uuid',
        'account_id',
        'playtime',
        'last_seen_at',
    ];

    protected $hidden = [
        
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'last_seen_at',
    ];


    /**
     * {@inheritDoc}
     */
    public function getBanIdentifier(): string {
        return $this->uuid;
    }

    /**
     * {@inheritDoc}
     */
    public function getBanReadableName(): string {
        $alias = $this->belongsTo('App\Modules\Players\MinecraftPlayerAlias', 'player_minecraft_id', 'player_minecraft_id')->latest();

        return $alias !== null
            ? $alias->alias
            : '';
    }

    
    public function account() {
        return $this->hasMany('App\Modules\Accounts\Models\Account', 'account_id', 'account_id');
    }

    public function aliases() {
        return $this->hasMany('App\Modules\Players\Models\MinecraftPlayerAlias', 'player_minecraft_id', 'player_minecraft_id');
    }
}
