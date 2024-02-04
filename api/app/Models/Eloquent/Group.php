<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Group extends Model
{
    use HasFactory;

    protected $table = 'groups';

    protected $primaryKey = 'group_id';

    protected $fillable = [
        'name',
        'alias',
        'is_build',
        'is_default',
        'is_staff',
        'is_admin',
        'minecraft_name',
        'discord_name',
        'can_access_panel',
    ];

    protected $casts = [
        'is_build' => 'boolean',
        'is_default' => 'boolean',
        'is_staff' => 'boolean',
        'is_admin' => 'boolean',
    ];

    public $timestamps = false;
}
