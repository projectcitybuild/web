<?php

namespace Entities\Models\Eloquent;

use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class ServerToken extends Model
{
    use HasFactory;

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
}
