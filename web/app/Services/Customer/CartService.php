<?php

namespace App\Services\Customer;

use App\Models\Cart;
use App\Repositories\Interfaces\CartRepositoryInterface;
use App\Repositories\Interfaces\DamagedItemRepositoryInterface;
use App\Repositories\Interfaces\ItemRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CartService
{
    /**
     * Inject repositories
     *
     * @param CartRepositoryInterface $cartRepo
     * @param ItemRepositoryInterface $itemRepo,
     * @param DamagedItemRepositoryInterface $damagedItemRepo
     */
    public function __construct(
        protected CartRepositoryInterface $cartRepo,
        protected ItemRepositoryInterface $itemRepo,
        protected DamagedItemRepositoryInterface $damagedItemRepo
    ) {}

    /**
     * Get user's cart with items
     *
     * @param int $userId
     * @return Collection
     */
    public function getUserCart(int $userId): Collection
    {
        return $this->cartRepo->getUserCart($userId);
    }

    /**
     * Add item to cart or update quantity
     *
     * @param int $userId
     * @param int $itemId
     * @param int $quantity
     * @param int|null $damagedItemId
     * @return Cart
     */
    public function addToCart(int $userId, int $itemId, int $quantity = 1, ?int $damagedItemId = null): Cart
    {
        $item = $this->itemRepo->find($itemId);

        if (! $item) {
            throw new \Exception('Item not found');
        }

        // Determine price and available stock based on damaged status
        $price = $item->unit_price;
        $availableStock = $item->quantity;

        if ($damagedItemId) {
            $damagedItem = $this->damagedItemRepo->query()
                ->where('id', $damagedItemId)
                ->where('item_id', $itemId)
                ->where('status', 'resellable')
                ->first();

            if (! $damagedItem) {
                throw new \Exception('Damaged item not found or not available');
            }

            $price = $damagedItem->discounted_price;
            $availableStock = $damagedItem->quantity;
        }

        if ($availableStock < $quantity) {
            throw new \Exception('Insufficient stock');
        }

        $existingCart = $this->cartRepo->findByUserAndItem($userId, $itemId, $damagedItemId);

        if ($existingCart) {
            $newQuantity = $existingCart->quantity + $quantity;

            if ($availableStock < $newQuantity) {
                throw new \Exception('Insufficient stock');
            }

            $this->cartRepo->update($existingCart->id, [
                'quantity' => $newQuantity,
                'price' => $price,
            ]);

            return $this->cartRepo->find($existingCart->id);
        }

        return $this->cartRepo->create([
            'user_id' => $userId,
            'item_id' => $itemId,
            'damaged_item_id' => $damagedItemId,
            'quantity' => $quantity,
            'price' => $price,
        ]);
    }

    /**
     * Update cart item quantity
     *
     * @param int $cartId
     * @param int $quantity
     * @return bool
     */
    public function updateQuantity(int $cartId, int $quantity): bool
    {
        $cart = $this->cartRepo->find($cartId);

        if (! $cart) {
            return false;
        }

        // Check correct stock source
        if ($cart->damaged_item_id) {
            $damagedItem = $this->damagedItemRepo->find($cart->damaged_item_id);

            if ($damagedItem->quantity < $quantity) {
                throw new \Exception('Insufficient stock');
            }
        } else {
            if ($cart->product->quantity < $quantity) {
                throw new \Exception('Insufficient stock');
            }
        }

        return $this->cartRepo->update($cartId, ['quantity' => $quantity]);
    }

    /**
     * Remove item from cart
     *
     * @param int $cartId
     * @return bool
     */
    public function removeFromCart(int $cartId): bool
    {
        return $this->cartRepo->delete($cartId);
    }

    /**
     * Clear user's entire cart
     *
     * @param int $userId
     * @return bool
     */
    public function clearCart(int $userId): bool
    {
        return $this->cartRepo->clearUserCart($userId);
    }

    /**
     * Get cart total for user
     *
     * @param int $userId
     * @return float
     */
    public function getCartTotal(int $userId): float
    {
        $cartItems = $this->getUserCart($userId);

        return $cartItems->sum(function ($cart) {
            return $cart->quantity * $cart->price;
        });
    }

    /**
     * Get cart item count for user
     *
     * @param int $userId
     * @return int
     */
    public function getCartCount(int $userId): int
    {
        return $this->getUserCart($userId)->sum('quantity');
    }

    /**
     * Toggle cart item selection
     *
     * @param int $cartId
     * @param bool $selected
     * @return bool
     */
    public function toggleSelection(int $cartId, bool $selected): bool
    {
        return $this->cartRepo->update($cartId, ['selected' => $selected]);
    }

    /**
     * Select/deselect all cart items for user
     *
     * @param int $userId
     * @param bool $selected
     * @return bool
     */
    public function toggleAllSelection(int $userId, bool $selected): bool
    {
        $cartItems = $this->getUserCart($userId);

        foreach ($cartItems as $item) {
            $this->cartRepo->update($item->id, ['selected' => $selected]);
        }

        return true;
    }

    /**
     * Get selected cart items only
     *
     * @param int $userId
     * @return Collection
     */
    public function getSelectedCartItems(int $userId): Collection
    {
        return $this->cartRepo->getSelectedUserCart($userId);
    }

    /**
     * Get cart total for selected items only
     *
     * @param int $userId
     * @return float
     */
    public function getSelectedCartTotal(int $userId): float
    {
        $selectedItems = $this->getSelectedCartItems($userId);

        return $selectedItems->sum(function ($cart) {
            return $cart->quantity * $cart->price;
        });
    }

    /**
     * Remove selected items from cart
     *
     * @param int $userId
     * @return bool
     */
    public function removeSelectedItems(int $userId): bool
    {
        return $this->cartRepo->removeSelectedCartItems($userId);
    }
}
