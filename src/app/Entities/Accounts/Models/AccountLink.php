<?php

namespace App\Entities\Accounts\Models;

use App\Model;

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
