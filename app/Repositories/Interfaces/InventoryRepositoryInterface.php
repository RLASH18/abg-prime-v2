<?php

namespace App\Repositories\Interfaces;

use App\Models\Inventory;

interface InventoryRepositoryInterface extends BaseRepositoryInterface
{
    public function latestByCategory(string $category): ?Inventory;
}
