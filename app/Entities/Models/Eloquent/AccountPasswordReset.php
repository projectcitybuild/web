<?php

namespace App\Entities\Models\Eloquent;

use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use function now;

/**
 * @property string email
 * @property string token
 * @property Carbon created_at
 */
final class AccountPasswordReset extends Model
{
    use HasFactory;

    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'account_password_resets';

    protected $primaryKey = 'email';

    protected $fillable = [
        'email',
        'token',
        'created_at',
    ];

    protected $hidden = [
    ];

    protected $dates = [
        'created_at',
    ];
}
