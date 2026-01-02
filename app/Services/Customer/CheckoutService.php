<?php

namespace App\Services\Customer;

use App\Repositories\Interfaces\CartRepositoryInterface;
use App\Repositories\Interfaces\ItemRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    /**
     * Inject required repositories
     *
     * @param CartRepositoryInterface $cartRepo
     * @param OrderRepositoryInterface $orderRepo
     * @param ItemRepositoryInterface $itemRepo
     */
    public function __construct(
        protected CartRepositoryInterface $cartRepo,
        protected OrderRepositoryInterface $orderRepo,
        protected ItemRepositoryInterface $itemRepo
    ) {}

    /**
     * Process checkout and create order from cart
     *
     * @param int $userId
     * @param array $checkoutData
     * @return int Order ID
     * @throws \Exception
     */
    public function processCheckout(int $userId, array $checkoutData): int
    {
        // Validate cart has items
        $cartItems = $this->cartRepo->getUserCart($userId);

        if ($cartItems->isEmpty()) {
            throw new \Exception('Your cart is empty');
        }

        // Validate stock availability
        $this->validateStock($cartItems);

        // Calculate totals
        $totalAmount = $this->calculateTotal($cartItems);

        DB::beginTransaction();
        try {
            // Create order
            $order = $this->orderRepo->create([
                'user_id' => $userId,
                'status' => 'pending',
                'payment_method' => $checkoutData['payment_method'],
                'delivery_method' => $checkoutData['delivery_method'],
                'delivery_address' => $checkoutData['delivery_address'] ?? null,
                'total_amount' => $totalAmount,
            ]);

            // Create order items and deduct stock
            foreach ($cartItems as $cartItem) {
                // Create order item
                $this->orderRepo->query()
                    ->find($order->id)
                    ->orderItems()
                    ->create([
                        'item_id' => $cartItem->item_id,
                        'quantity' => $cartItem->quantity,
                        'unit_price' => $cartItem->price
                    ]);

                // Deduct stock from product
                $product = $this->itemRepo->find($cartItem->item_id);
                $this->itemRepo->update($product->id, [
                    'quantity' => $product->quantity - $cartItem->quantity
                ]);
            }

            // Clear cart after successful order
            $this->cartRepo->clearUserCart($userId);

            DB::commit();

            return $order->id;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Failed to process checkout. Please try again.');
        }
    }

    /**
     * Validate stock availability for cart items
     *
     * @param \Illuminate\Database\Eloquent\Collection $cartItems
     * @return void
     * @throws \Exception
     */
    protected function validateStock($cartItems): void
    {
        foreach ($cartItems as $cartItem) {
            $product = $this->itemRepo->find($cartItem->item_id);

            if (! $product) {
                throw new \Exception('Product not found');
            }

            if ($product->quantity < $cartItem->quantity) {
                throw new \Exception('Insufficient stock');
            }
        }
    }

    /**
     * Calculate total amount from cart items
     *
     * @param \Illuminate\Database\Eloquent\Collection $cartItems
     * @return float
     */
    protected function calculateTotal($cartItems): float
    {
        return $cartItems->sum(function ($cartItem) {
            return $cartItem->price * $cartItem->quantity;
        });
    }

    /**
     * Get checkout summary
     *
     * @param int $userId
     * @return array
     * @throws \Exception
     */
    public function getCheckoutSummary(int $userId): array
    {
        $cartItems = $this->cartRepo->getUserCart($userId);

        if ($cartItems->isEmpty()) {
            throw new \Exception('Your cart is empty');
        }

        $subTotal = $this->calculateTotal($cartItems);

        return [
            'items' => $cartItems,
            'subTotal' => $subTotal,
            'total' => $subTotal,
            'item_count' => $cartItems->sum('quantity')
        ];
    }
}
