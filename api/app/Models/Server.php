<?php

namespace App\Models;

use Database\Factories\ServerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Server extends Model
{
    use HasFactory;

    protected $table = 'server';

    protected $fillable = [
        'name',
        'ip',
        'ip_alias',
        'port',
        'is_port_visible',
        'is_querying',
        'is_visible',
        'display_order',
        'game_type',
        'is_online',
        'num_of_players',
        'num_of_slots',
        'last_queried_at',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'last_queried_at',
    ];

    protected static function newFactory(): Factory
    {
        return ServerFactory::new();
    }
}
