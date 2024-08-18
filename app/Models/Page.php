<?php

namespace App\Models;

use App\Core\Domains\Auditing\AuditAttributes;
use App\Core\Domains\Auditing\Concerns\LogsActivity;
use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

final class Page extends Model implements LinkableAuditModel
{
    use HasFactory;
    use HasStaticTable;
    use LogsActivity;

    protected $table = 'pages';

    protected $primaryKey = 'page_id';

    protected $fillable = [
        'url',
        'title',
        'contents',
        'description',
        'is_draft',
    ];

    protected $hidden = [];

    protected $casts = [
        'is_draft' => 'boolean',
    ];

    public function auditAttributeConfig(): AuditAttributes
    {
        return AuditAttributes::build()
            ->add('url', 'title', 'description')
            ->addMultiline('contents')
            ->addBoolean('is_draft');
    }

    public function getActivitySubjectName(): ?string
    {
        return $this->title;
    }

    public function getActivitySubjectLink(): ?string
    {
        return route('front.panel.pages.edit', $this);
    }
}
