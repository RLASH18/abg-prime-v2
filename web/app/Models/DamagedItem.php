<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * Get all of the carts for the DamagedItem
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class, 'damaged_item_id');
    }
}
