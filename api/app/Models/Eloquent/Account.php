<?php

namespace App\Models\Eloquent;

use Database\Factories\AccountFactory;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

final class Account extends Authenticatable implements MustVerifyEmail, CanResetPassword
{
    use HasFactory;
    use HasApiTokens;
    use TwoFactorAuthenticatable;
    use Notifiable;

    protected $table = 'accounts';

    protected $primaryKey = 'account_id';

    protected $fillable = [
        'email',
        'username',
        'password',
        'remember_token',
        'last_login_ip',
        'last_login_at',
        'password_changed_at',
        'balance',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'totp_secret',
        'totp_backup_code',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'last_login_at',
        'password_changed_at',
        'email_verified_at',
    ];

    protected $casts = [
        'is_totp_enabled' => 'boolean',
    ];

    protected static function newFactory(): Factory
    {
        return AccountFactory::new();
    }

    public function isTwoFactorEnabled(): bool
    {
        return $this->two_factor_secret != null;
    }
}
