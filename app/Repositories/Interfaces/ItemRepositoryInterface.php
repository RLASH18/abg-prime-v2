<?php

namespace App\Repositories\Interfaces;

use App\Models\Item;

interface ItemRepositoryInterface extends BaseRepositoryInterface
{
    public function latestByCategory(string $category): ?Item;
}
