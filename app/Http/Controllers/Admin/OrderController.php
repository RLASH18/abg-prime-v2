<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\UpdateOrderStatusRequest;
use App\Services\OrderService;
use Illuminate\Http\Request;
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
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'search',
            'status',
            'payment_method',
            'delivery_method'
        ]);

        $orders = $this->orderService->getAllPaginated(9, $filters);

        return Inertia::render('admin/Orders/Index', [
            'orders' => $orders,
            'filters' => $filters,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $order = $this->orderService->find($id);

        if (! $order) {
            return $this->flashError('Order not found', 'admin.orders.index');
        }

        return Inertia::render('admin/Orders/Show', [
            'order' => $order,
        ]);
    }

    /**
     * Update order status.
     */
    public function updateStatus(UpdateOrderStatusRequest $request, int $id)
    {
        $status = $request->validated()['status'];
        $updated = $this->orderService->updateStatus($id, $status);

        if ($updated) {
            return $this->flashSuccess('Order status updated successfully', 'admin.orders.index');
        }

        return $this->flashError('Order not found', 'admin.orders.index');
    }
}
