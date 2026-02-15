<?php

namespace App\Models;

use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Role extends Model implements LinkableAuditModel
{
    use HasFactory;
    use HasStaticTable;

    protected $table = 'roles';
    protected $fillable = [
        'name',
        'alias',
        'is_default',
        'is_admin',
        'minecraft_name',
        'minecraft_display_name',
        'minecraft_hover_text',
        'display_priority',
        'role_type',
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
            table: 'roles_accounts',
            foreignPivotKey: 'role_id',
            relatedPivotKey: 'account_id',
        );
    }

    public function scopeWhereDefault(Builder $query)
    {
        $query->where('is_default', true);
    }

    public function scopeWhereBuildType(Builder $query)
    {
        $query->where('role_type', 'build');
    }

    public function scopeWhereTrustType(Builder $query)
    {
        $query->where('role_type', 'trust');
    }

    public function getActivitySubjectLink(): ?string
    {
        return route('manage.roles.index').'#role-'.$this->id;
    }

    public function getActivitySubjectName(): ?string
    {
        return "Role {$this->name}";
    }

    public static function getDonorOrThrow(): Role
    {
        $role = Role::where('name', 'donator')->first();
        throw_if($role === null, 'Could not find donor role');
        return $role;
    }
}
