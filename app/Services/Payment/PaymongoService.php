<?php

namespace App\Services\Payment;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymongoService
{
    protected string $secretKey;
    protected string $baseUrl = 'https://api.paymongo.com/v1';

    public function __construct()
    {
        $this->secretKey = config('services.paymongo.secret_key');
    }

    /**
     * Create checkout session for GCash/Bank Transfer
     *
     * @param Order $order
     * @return array
     */
    public function createCheckoutSession(Order $order): array
    {
        try {
            $lineItems = [];

            // Convert order items to PayMongo line items
            foreach ($order->orderItems as $item) {
                $lineItems[] = [
                    'currency' => 'PHP',
                    'amount' => (int) ($item->unit_price * 100), // Convert to centavos
                    'name' => $item->item->item_name ?? 'Product',
                    'quantity' => $item->quantity,
                ];
            }

            // Determine payment method
            $paymentMethods = match ($order->payment_method) {
                'gcash' => ['gcash'],
                'bank_transfer' => ['paymaya'],
                default => ['gcash', 'paymaya'],
            };

            // Make HTTP request to PayMongo API
            $response = Http::withBasicAuth($this->secretKey, '')
                ->post("{$this->baseUrl}/checkout_sessions", [
                    'data' => [
                        'attributes' => [
                            'send_email_receipt' => false,
                            'show_description' => true,
                            'show_line_items' => true,
                            'line_items' => $lineItems,
                            'payment_method_types' => $paymentMethods,
                            'success_url' => config('services.paymongo.success_url') . '?order_id=' . $order->id,
                            'cancel_url' => config('services.paymongo.failed_url') . '?order_id=' . $order->id,
                            'description' => 'Order #' . str_pad($order->id, 4, '0', STR_PAD_LEFT),
                            'metadata' => [
                                'order_id' => $order->id,
                                'user_id' => $order->user_id,
                            ],
                        ],
                    ],
                ]);

            if ($response->failed()) {
                throw new \Exception($response->json('errors.0.detail') ?? 'Payment session creation failed');
            }

            $session = $response->json('data');

            return [
                'checkout_url' => $session['attributes']['checkout_url'],
                'session_id' => $session['id'],
            ];
        } catch (\Exception $e) {
            Log::error('PayMongo checkout failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('Failed to create payment session');
        }
    }
}
