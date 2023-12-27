<?php

namespace App\Models\Eloquent;

use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

final class Badge extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'badges';
    protected $primaryKey = 'id';
    protected $fillable = [
        'display_name',
        'unicode_icon',
    ];
    public $timestamps = false;
}
