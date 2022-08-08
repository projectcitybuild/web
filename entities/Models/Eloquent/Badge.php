<?php

namespace Entities\Models\Eloquent;

use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Badge extends Model
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'badges';

    protected $primaryKey = 'id';

    protected $fillable = [
        'display_name',
        'unicode_icon',
    ];

    public $timestamps = false;
}
