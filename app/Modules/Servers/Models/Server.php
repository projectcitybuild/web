<?php

namespace App\Modules\Servers\Models;

use Illuminate\Database\Eloquent\Model;

class Server extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'servers';

    protected $primaryKey = 'server_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'ip',
        'ip_alias',
        'port',
        'is_port_visible',
        'is_querying',
        'is_visible',
        'display_order',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

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

    public function latestStatus() {
        return $this->belongsTo('App\Modules\Servers\Models\ServerStatus', 'server_id', 'server_id');
    }
}
