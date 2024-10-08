<?php

namespace App\Models;

use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Group extends Model implements LinkableAuditModel
{
    use HasFactory;
    use HasStaticTable;

    public const DONOR_GROUP_NAME = 'donator';

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

    protected $casts = [
        'is_build' => 'boolean',
        'is_default' => 'boolean',
        'is_staff' => 'boolean',
        'is_admin' => 'boolean',
    ];

    public $timestamps = false;

    public function accounts()
    {
        return $this->belongsToMany(
            related: Account::class,
            table: 'groups_accounts',
            foreignPivotKey: 'group_id',
            relatedPivotKey: 'account_id',
        );
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

    public function scopeWhereDefault(Builder $query)
    {
        $query->where('is_default', true);
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
