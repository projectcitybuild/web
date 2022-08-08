<?php

namespace Entities\Models\Eloquent;

use Altek\Eventually\Eventually;
use Carbon\Carbon;
use function collect;
use Entities\Models\PanelGroupScope;
use Entities\Resources\AccountResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Cashier\Billable;
use Laravel\Scout\Searchable;
use Library\Auditing\Traits\CausesActivity;
use Library\Auditing\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

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
    use LogsActivity;
    use Eventually;

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
    protected static $recordEvents = [
        'created',
        'updated',
        'deleted',
        'synced',
    ];
    protected $logged = [
        'email',
        'username',
        'activated',
        'group_names',
    ];
    protected $dates = [
        'created_at',
        'updated_at',
        'last_login_at',
    ];
    protected $casts = [
        'is_totp_enabled' => 'boolean',
    ];
    private ?Collection $cachedGroupScopes = null;

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

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Group::class,
            table: 'groups_accounts',
            foreignPivotKey: 'account_id',
            relatedPivotKey: 'group_id',
        );
    }

    public function getGroupNamesAttribute(): Collection
    {
        return $this->groups()->pluck('name');
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Badge::class,
            table: 'badges_pivot',
            foreignPivotKey: 'account_id',
            relatedPivotKey: 'badge_id',
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

    public function gameBans(): HasManyThrough
    {
        return $this->hasManyThrough(
            related: GameBan::class,
            through: MinecraftPlayer::class,
            firstKey: 'account_id',
            secondKey: 'banned_player_id',
            localKey: 'account_id',
            secondLocalKey: 'player_minecraft_id'
        );
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
        return $this->hasAbility(PanelGroupScope::ACCESS_PANEL->value);
    }

    public function hasAbility(string $to): bool
    {
        if ($this->cachedGroupScopes === null) {
            $this->cachedGroupScopes = $this->groups()
                ->with('groupScopes')
                ->get()
                ->flatMap(fn ($group) => $group->groupScopes->pluck('scope'))
                ->mapWithKeys(fn ($scope) => [$scope => true])  // Map to dictionary for faster lookup
                ?? collect();
        }

        return $this->cachedGroupScopes->has(key: $to);
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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->dontSubmitEmptyLogs()
            ->logOnly($this->logged)
            ->logOnlyDirty();
    }
}
