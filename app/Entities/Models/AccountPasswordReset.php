<?php

namespace App\Entities\Accounts\Models;

use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\URL;

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

    public function getPasswordResetUrl()
    {
        return URL::temporarySignedRoute('front.password-reset.edit', now()->addMinutes(20), [
            'token' => $this->token,
        ]);
    }
}
