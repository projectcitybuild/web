<?php

namespace App\Models;

use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

final class AccountActivation extends Model
{
    use HasStaticTable;
    use HasFactory;

    protected $table = 'account_activations';

    protected $fillable = [
        'account_id',
        'token',
        'created_at',
        'updated_at',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
