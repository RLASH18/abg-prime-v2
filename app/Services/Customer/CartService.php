<?php

namespace App\Services\Customer;

use App\Models\Cart;
use App\Repositories\Interfaces\CartRepositoryInterface;
use App\Repositories\Interfaces\ItemRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CartService
{
    /**
     * Inject repositories
     *
     * @param CartRepositoryInterface $cartRepo
     * @param ItemRepositoryInterface $itemRepo
     */
    public function __construct(
        protected CartRepositoryInterface $cartRepo,
        protected ItemRepositoryInterface $itemRepo
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
     * @return Cart
     */
    public function addToCart(int $userId, int $itemId, int $quantity = 1): Cart
    {
        $item = $this->itemRepo->find($itemId);

        if (! $item) {
            throw new \Exception('Item not found');
        }

        if ($item->quantity < $quantity) {
            throw new \Exception('Insufficient stock');
        }

        $existingCart = $this->cartRepo->findByUserAndItem($userId, $itemId);

        if ($existingCart) {
            $newQuantity = $existingCart->quantity + $quantity;

            if ($item->quantity < $newQuantity) {
                throw new \Exception('Insufficient stock');
            }

            $this->cartRepo->update($existingCart->id, [
                'quantity' => $newQuantity,
                'price' => $item->unit_price,
            ]);

            return $this->cartRepo->find($existingCart->id);
        }

        return $this->cartRepo->create([
            'user_id' => $userId,
            'item_id' => $itemId,
            'quantity' => $quantity,
            'price' => $item->unit_price
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

        if ($cart->product->quantity < $quantity) {
            throw new \Exception('Insufficient stock');
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
}
