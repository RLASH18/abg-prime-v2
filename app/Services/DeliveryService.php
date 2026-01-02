<?php

namespace App\Services;

use App\Models\Delivery;
use App\Repositories\Interfaces\DeliveryRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Traits\Filterable;
use App\Traits\HandlesFileUploads;
use App\Traits\SyncOrderDeliveryStatus;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

class DeliveryService
{
    use Filterable, SyncOrderDeliveryStatus, HandlesFileUploads;

    /**
     * Inject repositories
     *
     * @param DeliveryRepositoryInterface $deliveryRepo
     * @param OrderRepositoryInterface $orderRepo
     */
    public function __construct(
        protected DeliveryRepositoryInterface $deliveryRepo,
        protected OrderRepositoryInterface $orderRepo
    ) {}

    /**
     * Get paginated deliveries with filters
     *
     * @param int $perPage
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage, array $filters = [])
    {
        $query = $this->deliveryRepo->query()->with(['order.user', 'order.orderItems.item']);

        $this->applySearchFilter($query, $filters['search'] ?? null, ['driver_name', 'order.user.name']);
        $this->applyExactFilter($query, 'status', $filters['status'] ?? null);

        return $query->orderBy('scheduled_date', 'asc')->paginate($perPage);
    }

    /**
     * Find delivery by ID
     *
     * @param int $id
     * @return Delivery|null
     */
    public function findById(int $id)
    {
        return $this->deliveryRepo->query()
            ->with(['order.user', 'order.orderItems.item'])
            ->find($id);
    }

    /**
     * Find delivery by order ID
     *
     * @param int $orderId
     * @return Delivery|null
     */
    public function findByOrderId(int $orderId)
    {
        return $this->deliveryRepo->findByOrderId($orderId);
    }

    /**
     * Create delivery for an order
     *
     * @param int $orderId
     * @return Delivery|null
     */
    public function createDeliveryForOrder(int $orderId): ?Delivery
    {
        $order = $this->orderRepo->find($orderId);

        if (! $order || $order->delivery_method !== 'delivery') {
            return null;
        }

        // Check if delivery already exists
        $existingDelivery = $this->findByOrderId($orderId);

        if ($existingDelivery) {
            return $existingDelivery;
        }

        $deliveryData = [
            'order_id' => $orderId,
            'status' => 'scheduled',
            'scheduled_date' => now()->addDay()->toDateString(),
        ];

        return $this->deliveryRepo->create($deliveryData);
    }

    /**
     * Update delivery status and sync with order status
     *
     * @param int $deliveryId
     * @param string $deliveryStatus
     * @param array $validatedData
     * @param UploadedFile|null $proofFile
     * @return bool
     */
    public function updateDeliveryStatus(
        int $deliveryId,
        string $deliveryStatus,
        array $validatedData = [],
        ?UploadedFile $proofFile = null
    ): bool {
        $delivery = $this->deliveryRepo->find($deliveryId);

        if (! $delivery) {
            return false;
        }

        $additionalData = $this->buildAdditionalData($validatedData, $proofFile);
        $data = $this->prepareDeliveryData($deliveryStatus, $additionalData);

        $updated = $this->deliveryRepo->update($deliveryId, $data);

        if ($updated) {
            $this->handleOrderSync($delivery, $deliveryStatus);
        }

        return $updated;
    }

    /**
     * Update delivery status only (without syncing to order)
     *
     * @param int $deliveryId
     * @param string $deliveryStatus
     * @param array $additionalData
     * @return bool
     */
    public function updateDeliveryStatusOnly(int $deliveryId, string $deliveryStatus, array $additionalData = []): bool
    {
        $data = $this->prepareDeliveryData($deliveryStatus, $additionalData);
        return $this->deliveryRepo->update($deliveryId, $data);
    }

    /**
     * Build additional data from validated input and file upload
     *
     * @param array $validatedData
     * @param UploadedFile|null $proofFile
     * @return array
     */
    protected function buildAdditionalData(array $validatedData, ?UploadedFile $proofFile = null): array
    {
        $additionalData = array_filter(
            Arr::only($validatedData, ['driver_name', 'scheduled_date', 'remarks'])
        );

        if ($proofFile) {
            $additionalData['proof_of_delivery'] = $this->storeFile(
                $proofFile,
                'deliveries/proofs',
                'proof'
            );
        }

        return $additionalData;
    }

    /**
     * Prepare delivery data for update
     *
     * @param string $deliveryStatus
     * @param array $additionalData
     * @return array
     */
    protected function prepareDeliveryData(string $deliveryStatus, array $additionalData): array
    {
        $data = array_merge(['status' => $deliveryStatus], $additionalData);

        if ($deliveryStatus === 'delivered' && ! isset($data['actual_delivery_date'])) {
            $data['actual_delivery_date'] = now()->toDateString();
        }

        return $data;
    }

    /**
     * Handle order status synchronization
     *
     * @param Delivery $delivery
     * @param string $deliveryStatus
     * @return void
     */
    protected function handleOrderSync(Delivery $delivery, string $deliveryStatus): void
    {
        $mapping = $this->getDeliveryToOrderMapping();
        $orderStatus = $mapping[$deliveryStatus] ?? null;

        if (! $orderStatus) {
            return;
        }

        $order = $this->orderRepo->find($delivery->order_id);

        if (! $order || ! $this->shouldSync($order->status, $orderStatus)) {
            return;
        }

        $this->orderRepo->update($delivery->order_id, ['status' => $orderStatus]);
    }
}
