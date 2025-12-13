<?php

namespace App\Services;

use App\Models\Billing;
use App\Repositories\Interfaces\BillingRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Traits\Filterable;

class BillingService
{
    use Filterable;

    /**
     * Inject repositories
     *
     * @param BillingRepositoryInterface $billingRepo
     * @param OrderRepositoryInterface $orderRepo
     */
    public function __construct(
        protected BillingRepositoryInterface $billingRepo,
        protected OrderRepositoryInterface $orderRepo
    ) {}

    /**
     * Get paginated billings with filters
     *
     * @param int $perPage
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage, array $filters)
    {
        $query = $this->billingRepo->query()->with(['order.user', 'order.orderItems.item']);

        $this->applySearchFilter($query, $filters['search'] ?? null, ['billing_number', 'order.user.name']);
        $this->applyExactFilter($query, 'status', $filters['status'] ?? null);

        if (! empty($filters['payment_method'])) {
            $query->whereHas('order', function ($orderQuery) use ($filters) {
                $orderQuery->where('payment_method', $filters['payment_method']);
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Find billing by billing ID with relationships
     *
     * @param int $billingId
     * @return Billing|null
     */
    public function findById(int $billingId)
    {
        return $this->billingRepo->query()
            ->with(['order.user', 'order.orderItems.item'])
            ->find($billingId);
    }

    /**
     * Find billing by order ID
     *
     * @param int $orderId
     * @return Billing|null
     */
    public function findByOrderId(int $orderId)
    {
        return $this->billingRepo->findByOrderId($orderId);
    }

    /**
     * Create billing for an order
     *
     * @param int $orderId
     * @return Billing|null
     */
    public function createBillingForOrder(int $orderId): ?Billing
    {
        $order = $this->orderRepo->find($orderId);

        if (! $order) {
            return null;
        }

        // Check if billing already exits
        $existingBilling = $this->findByOrderId($orderId);

        if ($existingBilling) {
            return $existingBilling;
        }

        $billingNumber = $this->billingRepo->generateBillingNumber();
        $billingData = [
            'order_id' => $orderId,
            'billing_number' => $billingNumber,
            'amount' => $order->total_amount,
            'status' => 'unpaid',
        ];

        return $this->billingRepo->create($billingData);
    }

    /**
     * Mark billing as paid
     *
     * @param int $orderId
     * @return bool
     */
    public function markBillingAsPaid(int $orderId): bool
    {
        $billing = $this->findByOrderId($orderId);

        if (! $billing) {
            return false;
        }

        $data = [
            'status' => 'paid',
            'paid_at' => now(),
        ];

        return $this->billingRepo->update($billing->id, $data);
    }
}
