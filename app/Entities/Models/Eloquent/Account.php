<?php

namespace App\Entities\Models\Eloquent;

use App\Entities\Resources\AccountResource;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;
use function collect;
use function now;

/**
 * @property int account_id
 * @property string email
 * @property string username
 * @property string password
 * @property string remember_token
 * @property bool activated
 * @property bool is_totp_enabled
 * @property ?string last_login_ip
 * @property ?Carbon last_login_at
 * @property int balance
 */
final class Account extends Authenticatable
{
    use Notifiable;
    use Searchable;
    use HasFactory;
    use Billable;
    use HasApiTokens;

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
        'totp_secret',
        'totp_backup_code',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'last_login_at',
    ];

    protected $casts = [
        'is_totp_enabled' => 'boolean',
    ];

    public function toSearchableArray()
    {
        return [
            'account_id' => $this->getKey(),
            'email' => $this->email,
            'username' => $this->username,
        ];
    }

    public function minecraftAccount(): HasMany
    {
        return $this->hasMany('App\Entities\Models\Eloquent\MinecraftPlayer', 'account_id', 'account_id');
    }

    public function linkedSocialAccounts(): HasMany
    {
        return $this->hasMany('App\Entities\Models\Eloquent\AccountLink', 'account_id', 'account_id');
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'groups_accounts', 'account_id', 'group_id');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'account_id');
    }

    public function donationPerks(): HasMany
    {
        return $this->hasMany(DonationPerk::class, 'account_id', 'account_id');
    }

    public function emailChangeRequests(): HasMany
    {
        return $this->hasMany(AccountEmailChange::class, 'account_id');
    }

    public function gameBans()
    {
        // TODO: there's probably a way to optimise this just using the DB
        $bans = collect();

        foreach ($this->minecraftAccount as $minecraftAccount) {
            $bans = $bans->concat($minecraftAccount->gameBans);
        }

        return $bans;
    }

    public function isBanned()
    {
        // TODO: there's probably a way to optimise this just using the DB
        foreach ($this->minecraftAccount as $account) {
            if ($account->isBanned()) {
                return true;
            }
        }

        return false;
    }

    public function inGroup(Group $group)
    {
        return $this->groups->contains($group);
    }

    public function isAdmin()
    {
        return $this->groups()->where('is_admin', true)->count() > 0;
    }

    public function canAccessPanel()
    {
        return $this->groups()->where('can_access_panel', true)->count() > 0;
    }

    public function discourseGroupString()
    {
        $groups = $this->groups->pluck('discourse_name');

        return implode(',', array_filter($groups->toArray()));
    }

    /**
     * Gets an URL to the 'email change verification'
     * route with a signed signature to prevent
     * tampering.
     */
    public function getEmailChangeVerificationUrl(string $newEmail): string
    {
        return URL::temporarySignedRoute('front.account.settings.email.confirm', now()->addMinutes(15), [
            'old_email' => $this->email,
            'new_email' => $newEmail,
        ]);
    }

    /**
     * Gets an URL to the activation route with a
     * signed signature to prevent tampering.
     */
    public function getActivationUrl(): string
    {
        return URL::temporarySignedRoute('front.register.activate', now()->addDay(), [
            'email' => $this->email,
        ]);
    }

    public function updateLastLogin(string $ip)
    {
        $this->last_login_ip = $ip;
        $this->last_login_at = Carbon::now();
        $this->save();
    }

    public function resetTotp()
    {
        $this->totp_secret = null;
        $this->totp_backup_code = null;
        $this->totp_last_used = null;
        $this->is_totp_enabled = false;
    }

    public function toResource()
    {
        return AccountResource::make($this);
    }
}
