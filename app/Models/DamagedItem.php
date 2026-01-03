<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DamagedItem extends Model
{
    protected $fillable = [
        'item_id',
        'quantity',
        'discounted_price',
        'discount_percentage',
        'status',
        'remarks',
    ];

    /**
     * Get the item that owns the DamagedItem
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
