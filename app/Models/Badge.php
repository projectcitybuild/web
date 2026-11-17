<?php

namespace App\Models;

use App\Core\Domains\Auditing\AuditAttributes;
use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

final class Badge extends Model implements AuditableContract, LinkableAuditModel
{
    use Auditable;
    use HasFactory;
    use HasStaticTable;
    use Notifiable;

    protected $table = 'badges';
    protected $auditInclude = [
        'display_name',
        'unicode_icon',
    ];
    protected $fillable = [
        'display_name',
        'unicode_icon',
        'list_hidden',
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'list_hidden' => 'boolean',
    ];

    public function getActivitySubjectLink(): ?string
    {
        return route('manage.badges.edit', $this);
    }

    public function getActivitySubjectName(): ?string
    {
        return "Badge {$this->display_name}";
    }

    public function auditAttributeConfig(): AuditAttributes
    {
        return AuditAttributes::build()
            ->add('display_name', 'unicode_icon');
    }
}
