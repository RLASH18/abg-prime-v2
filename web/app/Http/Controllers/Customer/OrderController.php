<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Inject Order Service
     *
     * @param OrderService $orderService
     */
    public function __construct(
        protected OrderService $orderService
    ) {}

    /**
     * Display customer's orders
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'status',
            'payment_method',
            'date_from',
            'date_to'
        ]);

        $orders = $this->orderService->getCustomerOrders(
            Auth::id(),
            10,
            $filters
        );

        return Inertia::render('customer/Orders/Index', [
            'orders' => $orders,
            'filters' => $filters
        ]);
    }

    /**
     * Display single order details
     */
    public function show(int $id)
    {
        $order = $this->orderService->find($id);

        // Ensure customer can only view their own orders
        if (! $order || $order->user_id !== Auth::id()) {
            return $this->flashError('Order not found', 'customer.orders.index');
        }

        return Inertia::render('customer/Orders/Show', [
            'order' => $order
        ]);
    }
}
