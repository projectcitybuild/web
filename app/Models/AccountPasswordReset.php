<?php

namespace App\Models;

use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

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
}
