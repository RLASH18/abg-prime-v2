<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'item_id',
        'damaged_item_id',
        'quantity',
        'price',
        'selected'
    ];

    /**
     * Get the user that owns the Cart
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the product (item) for this cart entry
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    /**
     * Get the damaged item record if this is a damaged product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function damagedItem(): BelongsTo
    {
        return $this->belongsTo(DamagedItem::class, 'damaged_item_id');
    }
}
