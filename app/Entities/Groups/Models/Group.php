<?php

namespace App\Entities\Groups\Models;

use App\Entities\Accounts\Models\Account;
use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

final class Group extends Model
{
    use HasFactory;

    public const DONOR_GROUP_NAME = 'donator'; // Some day we'll get rid of this misspelling...

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'groups';

    protected $primaryKey = 'group_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'alias',
        'is_default',
        'is_staff',
        'is_admin',
        'discourse_name',
        'minecraft_name',
        'discord_name',
        'can_access_panel',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    public function accounts()
    {
        return $this->belongsToMany(Account::class, 'groups_accounts', 'group_id', 'account_id');
    }
}
