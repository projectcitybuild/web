<?php

namespace App\Models;

use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Prunable;

final class PasswordReset extends Model
{
    use HasStaticTable;
    use HasFactory;
    use Prunable;

    public $incrementing = false;

    protected $table = 'account_password_resets';

    protected $primaryKey = 'email';

    protected $fillable = [
        'email',
        'token',
        'created_at',
        'updated_at',
        'expires_at',
    ];

    public function scopeWhereToken(Builder $query, string $token)
    {
        $query->where('token', $token);
    }

    public function prunable(): Builder
    {
        return static::where('expires_at', '<=', now());
    }
}
