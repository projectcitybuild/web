<?php

namespace App\Models\Eloquent;

use App\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

final class Badge extends Model
{
    use HasFactory;
    use HasStaticTable;
    use Notifiable;

    protected $table = 'badge';

    protected $fillable = [
        'display_name',
        'unicode_icon',
    ];

    public $timestamps = false;
}
