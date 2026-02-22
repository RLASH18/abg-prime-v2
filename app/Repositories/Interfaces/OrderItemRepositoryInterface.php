<?php

namespace App\Repositories\Interfaces;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Collection;

interface OrderItemRepositoryInterface extends BaseRepositoryInterface
{
    public function getSalesHistory(int $itemId, int $days): Collection;
    public function getLatestSale(int $itemId): ?OrderItem;
}
