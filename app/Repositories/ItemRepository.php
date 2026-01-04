<?php

namespace App\Repositories;

use App\Models\Item;
use App\Repositories\Interfaces\ItemRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ItemRepository extends BaseRepository implements ItemRepositoryInterface
{
    /**
     * Inject the Item model
     *
     * @param Item $item
     */
    public function __construct(Item $item)
    {
        parent::__construct($item);
    }

    /**
     * Get the latest item by category
     *
     * @param string $category
     * @return Item|null
     */
    public function latestByCategory(string $category): ?Item
    {
        return $this->model->where('category', $category)->latest('id')->first();
    }

    /**
     * Get all items that are low or out of stock
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLowStockItems(): Collection
    {
        return $this->query()
            ->whereColumn('quantity', '<=', 'restock_threshold')
            ->orderByRaw('CASE WHEN quantity <= 0 THEN 0 ELSE 1 END')
            ->orderBy('quantity', 'asc')
            ->get();
    }
}
