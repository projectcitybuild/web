<?php

namespace App\Entities\Accounts\Models;

use App\Entities\Donations\Models\Donation;
use App\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;
use App\Entities\Groups\Models\Group;

final class Account extends Authenticatable
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

    public function donations()
    {
        return $this->hasMany(Donation::class, 'account_id');
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

    /**
     * Gets an URL to the activation route with a
     * signed signature to prevent tampering
     *
     * @return string
     */
    public function getActivationUrl() : string
    {
        return URL::temporarySignedRoute('front.register.activate', now()->addDay(), [
            'email' => $this->email,
        ]);
    }
}
