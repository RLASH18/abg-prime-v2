<?php

namespace App\Repositories\Interfaces;

interface DeliveryRepositoryInterface extends BaseRepositoryInterface
{
    public function findByOrderId(int $orderId);
}
