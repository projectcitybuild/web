<?php

namespace App\Models;

use Altek\Eventually\Eventually;
use App\Core\Domains\Auditing\AuditAttributes;
use App\Core\Domains\Auditing\Concerns\CausesActivity;
use App\Core\Domains\Auditing\Concerns\LogsActivity;
use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use App\Core\Utilities\Traits\HasStaticTable;
use App\Domains\Panel\Data\PanelGroupScope;
use App\Http\Resources\AccountResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Laravel\Cashier\Billable;
use Laravel\Passport\HasApiTokens;
use Laravel\Scout\Searchable;
use function collect;

final class Account extends Authenticatable implements LinkableAuditModel
{
    use Notifiable;
    use Searchable;
    use HasApiTokens;
    use HasStaticTable;
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

    protected $casts = [
        'last_login_at' => 'datetime',
        'is_totp_enabled' => 'boolean',
        'activated' => 'boolean',
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
            related: EmailChange::class,
            foreignKey: 'account_id',
        );
    }

    public function gamePlayerBans(): HasManyThrough
    {
        return $this->hasManyThrough(
            related: GamePlayerBan::class,
            through: MinecraftPlayer::class,
            firstKey: 'account_id',
            secondKey: 'banned_player_id',
            localKey: 'account_id',
            secondLocalKey: 'player_minecraft_id'
        );
    }

    public function warnings(): HasManyThrough
    {
        return $this->hasManyThrough(
            related: PlayerWarning::class,
            through: MinecraftPlayer::class,
            firstKey: 'account_id',
            secondKey: 'warned_player_id',
            localKey: 'account_id',
            secondLocalKey: 'player_minecraft_id'
        );
    }

    public function isBanned()
    {
        return $this->gamePlayerBans()->active()->exists();
    }

    public function banAppeals()
    {
        return BanAppeal::whereIn('game_ban_id', $this->gamePlayerBans()->pluck('id'));
    }

    public function inGroup(Group $group)
    {
        return $this->groups->contains($group);
    }

    public function hasBadge(Badge $badge)
    {
        return $this->badges->contains($badge);
    }

    public function isAdmin()
    {
        return $this->groups()->where('is_admin', true)->count() > 0;
    }

    public function canAccessPanel(): bool
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

    public function updatePassword(string $newPassword)
    {
        if (empty($newPassword)) {
            throw new \Exception('New password cannot be empty');
        }
        $this->password = Hash::make($newPassword);
        $this->save();
    }

    public function updateLastLogin(string $ip)
    {
        $this->last_login_ip = $ip;
        $this->last_login_at = now();
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

    public function auditAttributeConfig(): AuditAttributes
    {
        return AuditAttributes::build()
            ->add('email', 'username')
            ->addBoolean('activated')
            ->addArray('group_names');
    }

    public function getActivitySubjectLink(): ?string
    {
        return route('front.panel.accounts.show', $this);
    }

    public function getActivitySubjectName(): ?string
    {
        return $this->username;
    }

    public function scopeWhereEmail(Builder $query, string $email)
    {
        $query->where('email', $email);
    }
}
