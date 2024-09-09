<?php

namespace App\Models;

use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class EmailChange extends Model
{
    use HasStaticTable;
    use HasFactory;
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
            ownerKey: 'account_id',
        );
    }

    public function prunable(): Builder
    {
        return static::where('expires_at', '<=', now());
    }
}
