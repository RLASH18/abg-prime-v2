<?php

namespace App\Services\Customer;

use App\Models\Order;
use App\Notifications\OrderConfirmationNotification;
use App\Repositories\Interfaces\CartRepositoryInterface;
use App\Repositories\Interfaces\DamagedItemRepositoryInterface;
use App\Repositories\Interfaces\ItemRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Services\Payment\PaymongoService;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    /**
     * Inject required repositories
     *
     * @param CartRepositoryInterface $cartRepo
     * @param OrderRepositoryInterface $orderRepo
     * @param ItemRepositoryInterface $itemRepo
     * @param DamagedItemRepositoryInterface $damagedItemRepo
     * @param PaymongoService $paymongoService
     */
    public function __construct(
        protected CartRepositoryInterface $cartRepo,
        protected OrderRepositoryInterface $orderRepo,
        protected ItemRepositoryInterface $itemRepo,
        protected DamagedItemRepositoryInterface $damagedItemRepo,
        protected PaymongoService $paymongoService
    ) {}

    /**
     * Process checkout and create order from cart
     *
     * @param int $userId
     * @param array $checkoutData
     * @return int|array Order ID or array with checkout_url
     * @throws \Exception
     */
    public function processCheckout(int $userId, array $checkoutData): int|array
    {
        // Get SELECTED cart items only
        $cartItems = $this->cartRepo->getSelectedUserCart($userId);

        if ($cartItems->isEmpty()) {
            throw new \Exception('Please select products to checkout');
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
                if ($cartItem->damaged_item_id) {
                    $damagedItem = $this->damagedItemRepo->find($cartItem->damaged_item_id);

                    $damagedItem->decrement('quantity', $cartItem->quantity);
                } else {
                    $product = $this->itemRepo->find($cartItem->item_id);

                    $this->itemRepo->update($product->id, [
                        'quantity' => $product->quantity - $cartItem->quantity
                    ]);
                }
            }

            // Clear selected items from cart after successful order
            $this->cartRepo->removeSelectedCartItems($userId);

            // Handle payment method
            if (in_array($checkoutData['payment_method'], ['gcash', 'bank_transfer'])) {
                // Create PayMongo checkout session
                $session = $this->paymongoService->createCheckoutSession($order);

                // Save session id to order
                $this->orderRepo->update($order->id, [
                    'paymongo_session_id' => $session['session_id'],
                ]);

                DB::commit();

                // Return checkout URL for redirect
                return [
                    'checkout_url' => $session['checkout_url'],
                    'order_id' => $order->id,
                ];
            }

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
            if ($cartItem->damaged_item_id) {
                $damagedItem = $this->damagedItemRepo->find($cartItem->damaged_item_id);

                if (! $damagedItem || $damagedItem->quantity < $cartItem->quantity) {
                    throw new \Exception('Insufficient stock');
                }
            } else {
                $product = $this->itemRepo->find($cartItem->item_id);

                if (! $product) {
                    throw new \Exception('Product not found');
                }

                if ($product->quantity < $cartItem->quantity) {
                    throw new \Exception('Insufficient stock');
                }
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
        $cartItems = $this->cartRepo->getSelectedUserCart($userId);

        if ($cartItems->isEmpty()) {
            throw new \Exception('Please select items to checkout');
        }

        $subTotal = $this->calculateTotal($cartItems);

        return [
            'items' => $cartItems,
            'subTotal' => $subTotal,
            'total' => $subTotal,
            'item_count' => $cartItems->sum('quantity')
        ];
    }

    /**
     * Confirm payment and send notification
     *
     * @param Order $order
     * @return void
     */
    public function confirmPayment(Order $order): void
    {
        $this->orderRepo->update($order->id, [
            'status' => 'confirmed'
        ]);

        // Send order confirmation email
        $order->user->notify(new OrderConfirmationNotification($order->fresh()));
    }

    /**
     * Cancel payment
     *
     * @param Order $order
     * @return void
     */
    public function cancelPayment(Order $order): void
    {
        $this->orderRepo->update($order->id, [
            'status' => 'cancelled'
        ]);
    }
}
