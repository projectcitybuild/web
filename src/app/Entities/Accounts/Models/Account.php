<?php

namespace App\Entities\Accounts\Models;

use App\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;
use App\Entities\Groups\Models\Group;
use App\Entities\Players\Models\MinecraftPlayer;

final class Account extends Authenticatable
{
    use Notifiable;

    public const TABLE_NAME = 'accounts';

    public const COLUMN_ACCOUNT_ID = 'account_id';
    public const COLUMN_EMAIL = 'email';
    public const COLUMN_PASSWORD = 'password';
    public const COLUMN_REMEMBER_TOKEN = 'remember_token';
    public const COLUMN_LAST_LOGIN_IP = 'last_login_ip';
    public const COLUMN_LAST_LOGIN_AT = 'last_login_at';


    protected $table = self::TABLE_NAME;
    protected $primaryKey = self::COLUMN_ACCOUNT_ID;

    protected $fillable = [
        self::COLUMN_EMAIL,
        self::COLUMN_PASSWORD,
        self::COLUMN_REMEMBER_TOKEN,
        self::COLUMN_LAST_LOGIN_IP,
        self::COLUMN_LAST_LOGIN_AT,
    ];

    protected $hidden = [
        self::COLUMN_PASSWORD,
        self::COLUMN_REMEMBER_TOKEN,
        self::COLUMN_LAST_LOGIN_IP,
    ];

    protected $dates = [
        self::COLUMN_LAST_LOGIN_AT,
    ];

    
    public function minecraftAccount()
    {
        return $this->belongsTo(MinecraftPlayer::class, self::COLUMN_ACCOUNT_ID, self::COLUMN_ACCOUNT_ID);
    }

    public function linkedSocialAccounts()
    {
        return $this->hasMany(AccountLink::class, self::COLUMN_ACCOUNT_ID, self::COLUMN_ACCOUNT_ID);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'groups_accounts', self::COLUMN_ACCOUNT_ID, 'group_id');
    }

    /**
     * Gets an URL to the 'email change verification'
     * route with a signed signature to prevent
     * tampering
     *
     * @return string
     */
    public function getEmailChangeVerificationUrl(string $newEmail) : string
    {
        return URL::temporarySignedRoute('front.account.settings.email.confirm', now()->addMinutes(15), [
            'old_email' => $this->email,
            'new_email' => $newEmail,
        ]);
    }
}
