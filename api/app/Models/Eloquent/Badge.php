<?php

namespace App\Models\Eloquent;

use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Library\Auditing\AuditAttributes;
use Library\Auditing\Concerns\LogsActivity;
use Library\Auditing\Contracts\LinkableAuditModel;

final class Badge extends Model implements LinkableAuditModel
{
    use HasFactory;
    use Notifiable;
    use LogsActivity;

    protected $table = 'badges';
    protected $primaryKey = 'id';
    protected $fillable = [
        'display_name',
        'unicode_icon',
    ];
    public $timestamps = false;

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