<?php

namespace App\Models;

use App\Core\Domains\Auditing\AuditAttributes;
use App\Core\Domains\Auditing\Concerns\LogsActivity;
use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

final class Badge extends Model implements LinkableAuditModel
{
    use HasFactory;
    use HasStaticTable;
    use LogsActivity;
    use Notifiable;

    protected $table = 'badges';
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
