<?php

namespace App\Models;

use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class MinecraftWarp extends Model implements AuditableContract, LinkableAuditModel
{
    use Auditable;
    use HasFactory;
    use HasStaticTable;

    protected $table = 'minecraft_warps';
    protected $auditInclude = [
        'name',
        'world',
        'x',
        'y',
        'z',
        'pitch',
        'yaw',
    ];
    protected $fillable = [
        'name',
        'world',
        'x',
        'y',
        'z',
        'pitch',
        'yaw',
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'pitch' => 'decimal:1',
        'yaw' => 'decimal:1',
    ];

    public function getActivitySubjectLink(): ?string
    {
        return route('manage.minecraft.warps.edit', $this);
    }

    public function getActivitySubjectName(): ?string
    {
        return $this->name;
    }
}
