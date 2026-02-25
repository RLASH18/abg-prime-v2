<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\StoreCartRequest;
use App\Http\Requests\Cart\UpdateCartRequest;
use App\Services\Customer\CartService;
use Illuminate\Http\Request;
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
        $selectedTotal = $this->cartService->getSelectedCartTotal($userId);
        $selectedItems = $this->cartService->getSelectedCartItems($userId);

        return Inertia::render('customer/Cart', [
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
            'cartCount' => $cartCount,
            'selectedTotal' => $selectedTotal,
            'selectedCount' => $selectedItems->count(),
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
                $request->validated()['quantity'],
                $request->validated()['damaged_item_id'] ?? null
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

    /**
     * Toggle cart item selection
     */
    public function toggleSelection(int $id)
    {
        $cart = $this->cartService->getUserCart(Auth::id())
            ->firstWhere('id', $id);

        if (! $cart) {
            return $this->flashError('Cart item not found', 'customer.carts.index');
        }

        $this->cartService->toggleSelection($id, !$cart->selected);

        return back();
    }

    /**
     * Toggle all cart items selection
     */
    public function toggleAllSelection(Request $request)
    {
        $selected = $request->input('selected', true);
        $this->cartService->toggleAllSelection(Auth::id(), $selected);

        return back();
    }
}
