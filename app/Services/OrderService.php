<?php

namespace App\Services;

use App\Notifications\OrderConfirmationNotification;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Traits\Filterable;
use App\Traits\SyncOrderDeliveryStatus;

class OrderService
{
    use Filterable, SyncOrderDeliveryStatus;

    /**
     * Inject the order repository and billing service
     *
     * @param OrderRepositoryInterface $orderRepo
     * @param BillingService $billingService
     * @param DeliveryService $deliveryService
     */
    public function __construct(
        protected OrderRepositoryInterface $orderRepo,
        protected BillingService $billingService,
        protected DeliveryService $deliveryService
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

        $this->handleBillingSync($id, $status);
        $this->handleDeliveryCreation($id, $status);
        $this->handleDeliverySync($id, $status);

        if ($status === 'confirmed') {
            $this->sendOrderConfirmationEmail($id);
        }

        return true;
    }

    /**
     * Handle billing synchronization
     *
     * @param int $orderId
     * @param string $status
     * @return void
     */
    protected function handleBillingSync(int $orderId, string $status): void
    {
        if ($status === 'confirmed') {
            $this->billingService->createBillingForOrder($orderId);
        }

        if ($status === 'paid') {
            $this->billingService->markBillingAsPaid($orderId);
        }
    }

    /**
     * Handle delivery creation
     *
     * @param int $orderId
     * @param string $status
     * @return void
     */
    protected function handleDeliveryCreation(int $orderId, string $status): void
    {
        if ($status === 'assembled') {
            $this->deliveryService->createDeliveryForOrder($orderId);
        }
    }

    /**
     * Handle delivery status synchronization
     *
     * @param int $orderId
     * @param string $orderStatus
     * @return void
     */
    protected function handleDeliverySync(int $orderId, string $orderStatus): void
    {
        $mapping = $this->getOrderToDeliveryMapping();

        if (! isset($mapping[$orderStatus])) {
            return;
        }

        $delivery = $this->deliveryService->findByOrderId($orderId);

        if (! $delivery) {
            return;
        }

        $newDeliveryStatus = $mapping[$orderStatus];

        if (! $this->shouldSync($delivery->status, $newDeliveryStatus)) {
            return;
        }

        $this->deliveryService->updateDeliveryStatusOnly($delivery->id, $newDeliveryStatus);
    }

    /**
     * Send order confirmation email to customer
     *
     * @param int $orderId
     * @return void
     */
    protected function sendOrderConfirmationEmail(int $orderId): void
    {
        $order = $this->orderRepo->query()
            ->with(['user', 'orderItems.item'])
            ->find($orderId);

        if ($order && $order->user) {
            $order->user->notify(new OrderConfirmationNotification($order));
        }
    }
}
