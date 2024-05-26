<?php

namespace App\Models;

use App\Core\Domains\MinecraftUUID\MinecraftUUID;
use App\Core\Traits\HasStaticTable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;

final class Account extends Authenticatable implements MustVerifyEmail, CanResetPassword
{
    use HasFactory;
    use HasApiTokens;
    use Notifiable;
    use Billable;
    use HasStaticTable;

    protected $table = 'account';

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
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'last_login_at',
        'password_changed_at',
        'email_verified_at',
        'two_factor_confirmed_at',
    ];

    public function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $player = $this->player;
                if ($player !== null) {
                    $uuid = new MinecraftUUID($player->uuid);
                    return "https://minotar.net/avatar/".$uuid->trimmed();
                }
                if ($this->username !== null) {
                    $name = str_replace(search: " ", replace: "+", subject: $this->username);
                    return "https://ui-avatars.com/api/?name=".$name;
                }
                return null;
            }
        );
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(
            related: Player::class,
            foreignKey: Account::primaryKey(),
            ownerKey: Account::primaryKey(),
        );
    }

    public function donations(): HasMany
    {
        return $this->hasMany(
            related: Donation::class,
            foreignKey: Account::primaryKey(),
            localKey: Account::primaryKey(),
        );
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Badge::class,
            table: 'badges_pivot',
            foreignPivotKey: Account::primaryKey(),
            relatedPivotKey: Badge::primaryKey(),
        );
    }

    public function isTwoFactorEnabled(): bool
    {
        return $this->two_factor_confirmed_at != null;
    }
}
