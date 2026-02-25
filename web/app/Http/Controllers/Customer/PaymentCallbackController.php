<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Services\Customer\CheckoutService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentCallbackController extends Controller
{
    /**
     * Inject required services
     *
     * @param CheckoutService $checkoutService
     * @param OrderRepositoryInterface $orderRepo
     */
    public function __construct(
        protected CheckoutService $checkoutService,
        protected OrderRepositoryInterface $orderRepo
    ) {}

    /**
     * Payment success page
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function success(Request $request)
    {
        $orderId = $request->query('order_id');

        if ($orderId) {
            // Find order by ID using repository
            $order = $this->orderRepo->find($orderId);

            if ($order && $order->status === 'pending') {
                // Confirm payment using service
                $this->checkoutService->confirmPayment($order);
            }

            return Inertia::render('customer/PaymentCallback/Success', [
                'session_id' => $order?->paymongo_session_id,
                'message' => 'Payment successful! Your order is being processed.'
            ]);
        }

        return Inertia::render('customer/PaymentCallback/Success', [
            'session_id' => null,
            'message' => 'Payment successful! Your order is being processed.'
        ]);
    }

    /**
     * Payment failed page
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function failed(Request $request)
    {
        $orderId = $request->query('order_id');

        if ($orderId) {
            // Find order by ID using repository
            $order = $this->orderRepo->find($orderId);

            if ($order && $order->status === 'pending') {
                // Cancel payment using service
                $this->checkoutService->cancelPayment($order);
            }
        }

        return Inertia::render('customer/PaymentCallback/Failed', [
            'message' => 'Payment was cancelled or failed. Please try again.'
        ]);
    }
}
