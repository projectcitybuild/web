<?php

namespace App\Models;

use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

final class GroupScope extends Model
{
    use HasFactory;
    use HasStaticTable;

    protected $table = 'group_scopes';

    protected $primaryKey = 'id';

    protected $fillable = [
        'scope',
    ];

    public $timestamps = false;
}
