<?php

namespace Entities\Models\Eloquent;

use Altek\Eventually\Eventually;
use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Library\Auditing\AuditAttributes;
use Library\Auditing\Concerns\LogsActivity;
use Library\Auditing\Contracts\LinkableAuditModel;

final class ServerToken extends Model implements LinkableAuditModel
{
    use HasFactory;
    use LogsActivity;
    use Eventually;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'server_tokens';

    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'token',
        'server_id',
        'description',
    ];

    protected static $recordEvents = [
        'created',
        'updated',
        'deleted',
        'synced',
    ];
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function server(): HasOne
    {
        return $this->hasOne(
            related: Server::class,
            foreignKey: 'server_id',
            localKey: 'server_id',
        );
    }

    public function scopes(): BelongsToMany
    {
        return $this->belongsToMany(
            related: ServerTokenScope::class,
            table: 'server_token_scopes_pivot',
            foreignPivotKey: 'token_id',
            relatedPivotKey: 'scope_id',
            parentKey: 'id',
        );
    }

    public function getScopeNamesAttribute()
    {
        return $this->scopes()->pluck('scope');
    }

    public function getActivitySubjectLink(): ?string
    {
        return route('front.panel.server-tokens.edit', $this);
    }

    public function getActivitySubjectName(): ?string
    {
        return $this->description;
    }

    public function auditAttributeConfig(): AuditAttributes
    {
        return AuditAttributes::build()
            ->add('description', 'token')
            ->addRelationship('server_id', Server::class)
            ->addArray('scope_names');
    }
}
