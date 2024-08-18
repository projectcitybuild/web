<?php

namespace App\Models;

use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Group extends Model implements LinkableAuditModel
{
    use HasFactory;

    public const DONOR_GROUP_NAME = 'donator'; // Some day we'll get rid of this misspelling...

    protected $table = 'groups';

    protected $primaryKey = 'group_id';

    protected $fillable = [
        'name',
        'alias',
        'is_build',
        'is_default',
        'is_staff',
        'is_admin',
        'discourse_name',
        'minecraft_name',
        'discord_name',
        'can_access_panel',
    ];
    protected $hidden = [];
    protected $casts = [
        'is_build' => 'boolean',
        'is_default' => 'boolean',
        'is_staff' => 'boolean',
        'is_admin' => 'boolean',
    ];
    public $timestamps = false;

    public function accounts()
    {
        return $this->belongsToMany(Account::class, 'groups_accounts', 'group_id', 'account_id');
    }

    public function groupScopes(): BelongsToMany
    {
        return $this->belongsToMany(
            related: GroupScope::class,
            table: 'group_scopes_pivot',
            foreignPivotKey: 'group_id',
            relatedPivotKey: 'scope_id',
            parentKey: 'group_id',
            relatedKey: 'id',
        );
    }

    public function getActivitySubjectLink(): ?string
    {
        return route('front.panel.groups.index').'#group-'.$this->getKey();
    }

    public function getActivitySubjectName(): ?string
    {
        return "Group {$this->name}";
    }
}
