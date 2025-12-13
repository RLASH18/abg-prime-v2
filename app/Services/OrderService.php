<?php

namespace App\Services;

use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Traits\Filterable;

class OrderService
{
    use Filterable;

    /**
     * Inject the order repository and billing service
     *
     * @param OrderRepositoryInterface $orderRepo
     * @param BillingService $billingService
     */
    public function __construct(
        protected OrderRepositoryInterface $orderRepo,
        protected BillingService $billingService
    ) {}

    /**
     * Get paginated orders with filters
     *
     * @param int $perPage
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 10, array $filters = [])
    {
        $query = $this->orderRepo->query()->with(['user', 'orderItems.item']);

        $this->applySearchFilter($query, $filters['search'] ?? null, ['id', 'user.name']);
        $this->applyExactFilter($query, 'status', $filters['status'] ?? null);
        $this->applyExactFilter($query, 'payment_method', $filters['payment_method'] ?? null);
        $this->applyExactFilter($query, 'delivery_method', $filters['delivery_method'] ?? null);

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Find an order by ID
     *
     * @param int $id
     * @return Order|null
     */
    public function find(int $id)
    {
        return $this->orderRepo->query()
            ->with(['user', 'orderItems.item'])
            ->find($id);
    }

    /**
     * Update order status
     *
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateStatus(int $id, string $status): bool
    {
        $data = ['status' => $status];
        $updated = $this->orderRepo->update($id, $data);

        if (! $updated) {
            return false;
        }

        // Auto create billing when order is confirmed
        if ($status === 'confirmed') {
            $this->billingService->createBillingForOrder($id);
        }

        // Auto-update billing when order is paid
        if ($status === 'paid') {
            $this->billingService->markBillingAsPaid($id);
        }

        return true;
    }
}
