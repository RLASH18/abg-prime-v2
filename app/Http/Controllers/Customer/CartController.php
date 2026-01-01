<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\StoreCartRequest;
use App\Http\Requests\Cart\UpdateCartRequest;
use App\Services\Customer\CartService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CartController extends Controller
{
    /**
     * Inject CartService
     *
     * @param CartService $cartService
     */
    public function __construct(
        protected CartService $cartService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::id();
        $cartItems = $this->cartService->getUserCart($userId);
        $cartTotal = $this->cartService->getCartTotal($userId);
        $cartCount = $this->cartService->getCartCount($userId);

        return Inertia::render('customer/Cart/Index', [
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
            'cartCount' => $cartCount
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCartRequest $request)
    {
        try {
            $this->cartService->addToCart(
                Auth::id(),
                $request->validated()['item_id'],
                $request->validated()['quantity']
            );

            return $this->flashSuccess('Item added to cart', 'customer.carts.index');
        } catch (\Exception $e) {
            return $this->flashError($e->getMessage(), 'customer.homepage.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCartRequest $request, int $id)
    {
        try {
            $updated = $this->cartService->updateQuantity($id, $request->validated()['quantity']);

            if (! $updated) {
                return $this->flashError('Cart item not found', 'customer.carts.index');
            }

            return $this->flashSuccess('Cart updated successfully', 'customer.carts.index');
        } catch (\Exception $e) {
            return $this->flashError($e->getMessage(), 'customer.carts.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $deleted = $this->cartService->removeFromCart($id);

        if (! $deleted) {
            return $this->flashError('Cart item not found', 'customer.carts.index');
        }

        return $this->flashSuccess('Item removed from cart', 'customer.carts.index');
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        $this->cartService->clearCart(Auth::id());
        return $this->flashSuccess('Cart cleared successfully', 'customer.carts.index');
    }
}
