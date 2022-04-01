<?php

namespace App\Entities\Models\Eloquent;

use App\Model;

final class AccountLink extends Model
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
        return $this->belongsTo('App\Entities\Models\Eloquent\Account', 'account_id', 'account_id');
    }
}
