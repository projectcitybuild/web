<?php

namespace App\Entities\Models\Eloquent;

use App\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class MinecraftRedeemedLootBox extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'minecraft_redeemed_loot_boxes';

    protected $primaryKey = 'redeemed_loot_box_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id',
        'minecraft_loot_box_id',
        'created_at',
    ];

    protected $dates = [
        'created_at',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function lootBox(): BelongsTo
    {
        return $this->belongsTo(MinecraftLootBox::class, 'minecraft_loot_box_id', 'minecraft_loot_box_id');
    }
}
