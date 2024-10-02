<?php

namespace App\Models;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

/**
 * A quicker version of registration that allows the user to register
 * from inside of Minecraft, using just a code emailed to them
 */
final class MinecraftRegistration extends Model
{
    use HasStaticTable;
    use Prunable;

    protected $table = 'minecraft_registrations';

    protected $fillable = [
        'email',
        'minecraft_uuid',
        'minecraft_alias',
        'code',
        'expires_at',
    ];

    protected $casts = [
        'minecraft_uuid' => MinecraftUUID::class,
        'expires_at' => 'datetime',
    ];

    public function prunable(): Builder
    {
        return static::where('expires_at', '<=', now());
    }
}
