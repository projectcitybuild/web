<?php

namespace App\Entities\Accounts\Models;

use App\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;
use App\Entities\Groups\Models\Group;

/**
 * App\Entities\Accounts\Models\Account
 *
 * @property int $account_id
 * @property string $email
 * @property string $password
 * @property string|null $remember_token
 * @property string|null $last_login_ip
 * @property \Illuminate\Support\Carbon|null $last_login_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Groups\Models\Group[] $groups
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Accounts\Models\AccountLink[] $linkedSocialAccounts
 * @property-read \App\Entities\Players\Models\MinecraftPlayer $minecraftAccount
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\Account newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\Account newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\Account query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\Account whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\Account whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\Account whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\Account whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\Account whereLastLoginIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\Account wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\Account whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\Account whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Account extends Authenticatable
{
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
    ];

    protected $hidden = [

    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'last_login_at',
    ];

    public function minecraftAccount()
    {
        return $this->hasMany('App\Entities\Players\Models\MinecraftPlayer', 'account_id', 'account_id');
    }

    public function linkedSocialAccounts()
    {
        return $this->hasMany('App\Entities\Accounts\Models\AccountLink', 'account_id', 'account_id');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'groups_accounts', 'account_id', 'group_id');
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
