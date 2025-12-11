<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
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
}
