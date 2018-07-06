<?php

namespace App\Modules\Servers\Models;

use App\Support\Model;

class Server extends Model {

    protected $table = 'servers';

    protected $primaryKey = 'server_id';

    protected $fillable = [
        'name',
        'server_category_id',
        'ip',
        'ip_alias',
        'port',
        'is_port_visible',
        'is_querying',
        'is_visible',
        'display_order',
        'game_type',
    ];

    protected $hidden = [
        
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * Gets the ip address of the server (with port depending on availability)
     *
     * @return string
     */
    public function getAddress() : string {
        $port = $this->port != null && $this->is_port_visible
            ? ':'.$this->port
            : '';

        return $this->ip . $port;
    }


    public function category() {
        return $this->hasOne('App\Modules\Servers\Models\ServerCategory', 'server_category_id', 'server_category_id');
    }

    public function status() {
        return $this->belongsTo('App\Modules\Servers\Models\ServerStatus', 'server_id', 'server_id')
            ->take(1)
            ->latest();
    }
    

    public function isOnline() {
        return $this->status && $this->status->is_online;
    }
}
