<?php

namespace App\Repositories\Interfaces;

use App\Models\Item;
use Illuminate\Database\Eloquent\Collection;

interface ItemRepositoryInterface extends BaseRepositoryInterface
{
    public function latestByCategory(string $category): ?Item;
    public function getLowStockItems(): Collection;
}
