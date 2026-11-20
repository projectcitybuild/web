<?php

namespace App\Models;

use App\Core\Domains\Auditing\AuditAttributes;
use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

final class Role extends Model implements AuditableContract, LinkableAuditModel
{
    use Auditable;
    use HasFactory;
    use HasStaticTable;

    protected $table = 'roles';
    protected $auditInclude = [
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

    public function permissions()
    {
        return $this->belongsToMany(
            related: Permission::class,
            table: 'roles_permissions'
        );
    }

    public function accounts()
    {
        return $this->belongsToMany(
            related: Account::class,
            table: 'roles_accounts',
            foreignPivotKey: 'role_id',
            relatedPivotKey: 'account_id',
        );
    }

    public function discordRoleMappings(): HasMany
    {
        return $this->hasMany(DiscordRoleMapping::class);
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

    public function auditAttributeConfig(): AuditAttributes
    {
        return AuditAttributes::build()
            ->add(
                'name',
                'alias',
                'minecraft_name',
                'minecraft_display_name',
                'minecraft_hover_text',
                'display_priority',
                'role_type',
                'additional_homes',
            )
            ->addBoolean('is_default', 'is_admin')
            ->addArray('permission_names');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function transformAudit(array $data): array
    {
        foreach (['old_values', 'new_values'] as $side) {
            if (array_key_exists('permissions', $data[$side] ?? [])) {
                $data[$side]['permission_names'] = collect($data[$side]['permissions'])
                    ->pluck('name')
                    ->all();
                unset($data[$side]['permissions']);
            }
        }

        return $data;
    }

    public static function getDonorOrThrow(): Role
    {
        $role = Role::where('name', 'donator')->first();
        throw_if($role === null, 'Could not find donor role');
        return $role;
    }
}
