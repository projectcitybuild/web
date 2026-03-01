<?php

namespace App\Models;

use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class EmailChange extends Model
{
    use HasFactory;
    use HasStaticTable;
    use Prunable;

    protected $table = 'account_email_changes';
    protected $fillable = [
        'account_id',
        'token',
        'email',
        'expires_at',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(
            related: Account::class,
            foreignKey: 'account_id',
        );
    }

    public function scopeWhereToken(Builder $query, string $token)
    {
        $query->where('token', $token);
    }

    public function scopeWhereActive(Builder $query)
    {
        $query->where('expires_at', '>', now());
    }

    public function prunable(): Builder
    {
        return self::where('expires_at', '<=', now());
    }
}
