<?php

namespace App\Models\Eloquent;

use App\Model;
use App\Traits\HasStaticTable;
use Database\Factories\BadgeFactory;
use Database\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

final class Badge extends Model
{
    use HasFactory;
    use HasStaticTable;
    use Notifiable;

    protected $table = 'badges';

    protected $primaryKey = 'id';

    protected $fillable = [
        'display_name',
        'unicode_icon',
    ];

    public $timestamps = false;

    protected static function newFactory(): Factory
    {
        return BadgeFactory::new();
    }
}
