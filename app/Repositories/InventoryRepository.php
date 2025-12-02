<?php

namespace App\Repositories;

use App\Models\Inventory;
use App\Repositories\Interfaces\InventoryRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class InventoryRepository extends BaseRepository implements InventoryRepositoryInterface
{
    /**
     * Inject the Inventory model
     * 
     * @param Inventory $inventory
     */
    public function __construct(Inventory $inventory)
    {
        parent::__construct($inventory);
    }

    /**
     * Get the latest inventory item by category
     * 
     * @param string $category
     * @return Inventory|null
     */
    public function latestByCategory(string $category): ?Inventory
    {
        return $this->model->where('category', $category)->latest('id')->first();
    }
}
