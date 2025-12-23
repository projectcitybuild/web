<?php

namespace App\Models;

use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

final class AccountActivation extends Model
{
    use HasFactory;
    use HasStaticTable;
    use Prunable;

    protected $table = 'account_activations';
    protected $fillable = [
        'account_id',
        'token',
        'created_at',
        'updated_at',
        'expires_at',
    ];
    protected $hidden = [
        'token',
    ];
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function scopeWhereNotExpired(Builder $query)
    {
        $query->where('expires_at', '>', now());
    }

    public function prunable(): Builder
    {
        return self::where('expires_at', '<=', now());
    }
}
