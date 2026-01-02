<?php

namespace App\Repositories\Interfaces;

use App\Models\Cart;
use Illuminate\Database\Eloquent\Collection;

interface CartRepositoryInterface extends BaseRepositoryInterface
{
    public function getUserCart(int $userId): Collection;
    public function findByUserAndItem(int $userId, int $itemId): ?Cart;
    public function clearUserCart(int $userId): bool;
    public function getSelectedUserCart(int $userId): Collection;
}
