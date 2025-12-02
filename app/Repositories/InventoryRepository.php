<?php

namespace App\Repositories;

use App\Models\Inventory;
use App\Repositories\Interfaces\InventoryRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class InventoryRepository extends BaseRepository implements InventoryRepositoryInterface
{
    public function __construct(Inventory $inventory)
    {
        parent::__construct($inventory);
    }

    public function latestByCategory(string $category): ?Inventory
    {
        return $this->model->where('category', $category)->latest('id')->first();
    }
}
