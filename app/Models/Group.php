<?php

namespace App\Models;

use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Group extends Model implements LinkableAuditModel
{
    use HasFactory;
    use HasStaticTable;

    protected $table = 'groups';
    protected $primaryKey = 'group_id';
    protected $fillable = [
        'name',
        'alias',
        'is_default',
        'is_admin',
        'minecraft_name',
        'minecraft_display_name',
        'minecraft_hover_text',
        'display_priority',
        'group_type',
        'additional_homes',
    ];
    protected $casts = [
        'is_default' => 'boolean',
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

    public function scopeWhereDefault(Builder $query)
    {
        $query->where('is_default', true);
    }

    public function scopeWhereBuildType(Builder $query)
    {
        $query->where('group_type', 'build');
    }

    public function scopeWhereTrustType(Builder $query)
    {
        $query->where('group_type', 'trust');
    }

    public function getActivitySubjectLink(): ?string
    {
        return route('manage.groups.index').'#group-'.$this->getKey();
    }

    public function getActivitySubjectName(): ?string
    {
        return "Group {$this->name}";
    }

    public static function getDonorOrThrow(): Group
    {
        $group = Group::where('name', 'donator')->first();
        throw_if($group === null, 'Could not find donor group');
        return $group;
    }
}
