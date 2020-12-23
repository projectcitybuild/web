<?php

namespace App\Entities\Groups\Models;

use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

final class Group extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     */
    public bool $timestamps = false;
    /**
     * The table associated with the model.
     */
    protected string $table = 'groups';

    protected $primaryKey = 'group_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = [
        'name',
        'alias',
        'is_default',
        'is_staff',
        'is_admin',
        'discourse_name',
        'can_access_panel',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected array $hidden = [];
}
