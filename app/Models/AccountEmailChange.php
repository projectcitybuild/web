<?php

namespace App\Models;

use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class AccountEmailChange extends Model
{
    use HasFactory;

    protected $table = 'account_email_changes';

    protected $primaryKey = 'account_email_change_id';

    protected $fillable = [
        'account_id',
        'token',
        'email_previous',
        'email_new',
        'is_previous_confirmed',
        'is_new_confirmed',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(
            related: Account::class,
            foreignKey: 'account_id',
            ownerKey: 'account_id',
        );
    }
}
