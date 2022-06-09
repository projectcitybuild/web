<?php

namespace Entities\Models\Eloquent;

use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Library\Auditing\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

final class Page extends Model
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


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->dontSubmitEmptyLogs()
            ->logOnlyDirty()
            ->logExcept(['page_id', 'created_at', 'updated_at']);
    }

    public function getActivitySubjectLink(): ?string
    {
        return route('front.panel.pages.edit', $this);
    }

    public function getActivitySubjectName(): ?string
    {
        return $this->title;
    }
}
