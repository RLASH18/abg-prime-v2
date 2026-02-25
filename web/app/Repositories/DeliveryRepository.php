<?php

namespace App\Repositories;

use App\Models\Delivery;
use App\Repositories\Interfaces\DeliveryRepositoryInterface;

class DeliveryRepository extends BaseRepository implements DeliveryRepositoryInterface
{
    /**
     * Inject the Delivery model
     *
     * @param Delivery $delivery
     */
    public function __construct(Delivery $delivery)
    {
        parent::__construct($delivery);
    }

    /**
     * Find delivery by order ID
     *
     * @param int $orderId
     * @return Delivery|null
     */
    public function findByOrderId(int $orderId)
    {
        return $this->model
            ->where('order_id', $orderId)
            ->first();
    }
}
