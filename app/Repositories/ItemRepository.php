<?php

namespace App\Repositories;

use App\Models\Item;
use App\Repositories\Interfaces\ItemRepositoryInterface;

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
}
