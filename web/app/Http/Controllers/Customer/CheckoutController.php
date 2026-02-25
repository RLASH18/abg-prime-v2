<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Checkout\ProcessCheckoutRequest;
use App\Services\Customer\CheckoutService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    /**
     * Inject checkout service
     *
     * @param CheckoutService $checkoutService
     */
    public function __construct(
        protected CheckoutService $checkoutService
    ) {}

    public function index()
    {
        try {
            $summary = $this->checkoutService->getCheckoutSummary(Auth::id());

            return Inertia::render('customer/Checkout', $summary);
        } catch (\Exception $e) {
            return $this->flashError($e->getMessage(), 'customer.cart.index');
        }
    }

    /**
     * Process checkout
     */
    public function process(ProcessCheckoutRequest $request)
    {
        try {
            $result = $this->checkoutService->processCheckout(
                Auth::id(),
                $request->validated()
            );

            // If PayMongo payment, redirect to checkout URL
            if (isset($result['checkout_url'])) {
                return Inertia::location($result['checkout_url']);
            }

            // Cash payment - redirect to order page
            return $this->flashSuccess(
                'Order placed successfully!',
                'customer.orders.show',
                ['id' => $result]
            );
        } catch (\Exception $e) {
            return $this->flashError($e->getMessage(), 'customer.checkout.index');
        }
    }
}
