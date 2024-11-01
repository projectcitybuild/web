<?php

namespace App\Models;

use App\Core\Domains\Auditing\AuditAttributes;
use App\Core\Domains\Auditing\Concerns\LogsActivity;
use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

final class Badge extends Model implements LinkableAuditModel
{
    use HasFactory;
    use HasStaticTable;
    use Notifiable;
    use LogsActivity;

    protected $table = 'badges';

    protected $fillable = [
        'display_name',
        'unicode_icon',
        'list_hidden',
        'created_at',
        'updated_at',
    ];

    public function getActivitySubjectLink(): ?string
    {
        return route('front.panel.badges.edit', $this);
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
