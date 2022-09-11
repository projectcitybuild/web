<?php

namespace Entities\Models\Eloquent;

use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Library\Auditing\AuditAttributes;
use Library\Auditing\Concerns\LogsActivity;
use Library\Auditing\Contracts\LinkableAuditModel;

final class Page extends Model implements LinkableAuditModel
{
    use HasFactory;
    use LogsActivity;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pages';

    protected $primaryKey = 'page_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url',
        'title',
        'contents',
        'description',
        'is_draft',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
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
