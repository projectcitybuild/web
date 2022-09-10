<?php

namespace Entities\Models\Eloquent;

use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

final class GroupScope extends Model
{
    use HasFactory;

    protected $table = 'group_scopes';
    protected $primaryKey = 'id';
    protected $fillable = [
        'scope',
    ];

    public $timestamps = false;
}
