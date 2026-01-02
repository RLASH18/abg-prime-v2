<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Repositories\Interfaces\CartRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CartRepository extends BaseRepository implements CartRepositoryInterface
{
    /**
     * Inject the Cart model
     *
     * @param Cart $cart
     */
    public function __construct(Cart $cart)
    {
        parent::__construct($cart);
    }

    /**
     * Get user's cart items with item details
     *
     * @param int $userId
     * @return Collection
     */
    public function getUserCart(int $userId): Collection
    {
        return $this->query()
            ->with('product')
            ->where('user_id', $userId)
            ->get();
    }

    /**
     * Find cart item by user and item
     *
     * @param int $userId
     * @param int $itemId
     * @return Cart|null
     */
    public function findByUserAndItem(int $userId, int $itemId): ?Cart
    {
        return $this->query()
            ->where('user_id', $userId)
            ->where('item_id', $itemId)
            ->first();
    }

    /**
     * Clear all items from user's cart
     *
     * @param int $userId
     * @return bool
     */
    public function clearUserCart(int $userId): bool
    {
        return $this->query()
            ->where('user_id', $userId)
            ->delete() > 0;
    }

    /**
     * Get user's selected cart items
     *
     * @param int $userId
     * @return Collection
     */
    public function getSelectedUserCart(int $userId): Collection
    {
        return $this->query()
            ->where('user_id', $userId)
            ->where('selected', true)
            ->with('product')
            ->get();
    }
}
