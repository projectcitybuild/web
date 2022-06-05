<?php

namespace Entities\Models\Eloquent;

use Carbon\Carbon;
use Entities\Resources\AccountResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Scout\Searchable;
use Library\Auditing\Traits\CausesActivity;

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
    use CausesActivity;

    protected $table = 'accounts';

    protected $primaryKey = 'account_id';

    protected $fillable = [
        'email',
        'username',
        'password',
        'remember_token',
        'last_login_ip',
        'last_login_at',
        'balance',
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
        return $this->hasMany(
            related: MinecraftPlayer::class,
            foreignKey: 'account_id',
            localKey: 'account_id',
        );
    }

    public function linkedSocialAccounts(): HasMany
    {
        return $this->hasMany(
            related: AccountLink::class,
            foreignKey: 'account_id',
            localKey: 'account_id',
        );
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Group::class,
            table: 'groups_accounts',
            foreignPivotKey: 'account_id',
            relatedPivotKey: 'group_id',
        );
    }

    public function donations(): HasMany
    {
        return $this->hasMany(
            related: Donation::class,
            foreignKey: 'account_id',
        );
    }

    public function donationPerks(): HasMany
    {
        return $this->hasMany(
            related: DonationPerk::class,
            foreignKey: 'account_id',
            localKey: 'account_id',
        );
    }

    public function emailChangeRequests(): HasMany
    {
        return $this->hasMany(
            related: AccountEmailChange::class,
            foreignKey: 'account_id',
        );
    }

    public function gameBans()
    {
        return GameBan::whereIn('banned_player_id', $this->minecraftAccount()->pluck('player_minecraft_id'));
    }

    public function isBanned()
    {
        return $this->gameBans()->active()->exists();
    }

    public function banAppeals()
    {
        return BanAppeal::whereIn('game_ban_id', $this->gameBans()->pluck('game_ban_id'));
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
        return new AccountResource($this);
    }

    public function getActivitySubjectLink(): ?string
    {
        return route('front.panel.accounts.show', $this);
    }

    public function getActivitySubjectName(): ?string
    {
        return $this->username;
    }


}
