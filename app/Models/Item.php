<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'item_code',
        'brand_name',
        'item_name',
        'description',
        'category',
        'item_image_1',
        'item_image_2',
        'item_image_3',
        'unit_price',
        'quantity',
        'restock_threshold'
    ];

    /**
     * Get the supplier that owns the Inventory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /**
     * Get all of the damaged items for the Item
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function damagedItems(): HasMany
    {
        return $this->hasMany(DamagedItem::class, 'item_id');
    }

    /**
     * Get all of the order items for the Item
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'item_id');
    }

    /**
     * Get all of the carts for the Item
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class, 'item_id');
    }
}
