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
            ->with(['product', 'damagedItem'])
            ->where('user_id', $userId)
            ->get();
    }

    /**
    * Find cart item by user, item, and damaged item
     *
     * @param int $userId
     * @param int $itemId
     * @param int|null $damagedItemId
     * @return Cart|null
     */
    public function findByUserAndItem(int $userId, int $itemId, ?int $damagedItemId = null): ?Cart
    {
        return $this->query()
            ->where('user_id', $userId)
            ->where('item_id', $itemId)
            ->where('damaged_item_id', $damagedItemId)
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
            ->with(['product', 'damagedItem'])
            ->get();
    }

    /**
     * Remove selected items from user's cart
     *
     * @param int $userId
     * @return bool
     */
    public function removeSelectedCartItems(int $userId): bool
    {
        return $this->query()
            ->where('user_id', $userId)
            ->where('selected', true)
            ->delete() > 0;
    }
}
