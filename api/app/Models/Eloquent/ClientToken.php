<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class ClientToken extends Model
{
    use HasFactory;

    protected $table = 'client_token';

    protected $fillable = [
        'client',
        'token',
        'scope',
    ];

    protected $dates = [
        'expires_at',
        'created_at',
        'updated_at',
    ];

    function permittedScopes(): array
    {
        $scopes = explode(separator: ",", string: $this->scope);

        return collect($scopes)
            ->map(fn ($e) => strtolower($e))
            ->toArray();
    }
}
