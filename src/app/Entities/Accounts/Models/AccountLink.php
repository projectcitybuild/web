<?php

namespace App\Entities\Accounts\Models;

use App\Model;

/**
 * App\Entities\Accounts\Models\AccountLink
 *
 * @property int $account_link_id
 * @property int $account_id
 * @property string|null $provider_name
 * @property string|null $provider_id
 * @property string $provider_email
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Entities\Accounts\Models\Account $account
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\AccountLink newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\AccountLink newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\AccountLink query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\AccountLink whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\AccountLink whereAccountLinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\AccountLink whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\AccountLink whereProviderEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\AccountLink whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\AccountLink whereProviderName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\AccountLink whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AccountLink extends Model
{
    protected $table = 'account_links';

    protected $primaryKey = 'account_link_id';

    protected $fillable = [
        'account_id',
        'provider_name',
        'provider_id',
        'provider_email',
    ];

    protected $hidden = [
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function account()
    {
        return $this->belongsTo('App\Entities\Accounts\Models\Account', 'account_id', 'account_id');
    }
}
